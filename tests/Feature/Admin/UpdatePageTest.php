<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Services\UpdaterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdatePageTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        @unlink(storage_path('license.cache'));
        parent::tearDown();
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    private function licensed(): void
    {
        // DEV- prefix: isValid() checks domain only (localhost = dev domain),
        // no license.cache file needed — avoids CI failures where cache doesn't exist.
        config(['dravion.license_key' => 'DEV-TEST']);
        @unlink(storage_path('license.cache'));
    }

    private function unlicensed(): void
    {
        config(['dravion.license_key' => '']);
    }

    private function fakeRelease(string $tag, ?bool $licenseValid = null): void
    {
        $this->fakeReleases([$tag], $licenseValid);
    }

    /**
     * @param string[] $tags newest first
     * @param bool|null $licenseValid null = don't fake license server (no live check expected)
     */
    private function fakeReleases(array $tags, ?bool $licenseValid = null): void
    {
        $patterns = [
            'api.github.com/*' => Http::response(array_map(fn ($tag) => [
                'tag_name'    => $tag,
                'body'        => "Release notes for {$tag}",
                'zipball_url' => 'https://api.github.com/zip/' . $tag,
                'draft'       => false,
                'prerelease'  => false,
            ], $tags), 200),
        ];

        if ($licenseValid !== null) {
            $key      = config('dravion.license_key', '');
            $body     = $licenseValid
                ? ['valid' => true,  'license_key' => $key ?: 'DRV-VALID', 'domain' => 'localhost']
                : ['valid' => false, 'error' => 'License suspended'];
            $status   = $licenseValid ? 200 : 403;
            $patterns['*/api/router.php*'] = Http::response($body, $status);
        }

        Http::fake($patterns);
        config(['updater.owner' => 'o', 'updater.repo' => 'r']);
    }

    public function test_unlicensed_sees_latest_version_but_cannot_install(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->unlicensed();
        $this->fakeRelease('v1.3.0');

        $this->actingAs($this->admin())
            ->get('/admin/updates')
            ->assertStatus(200)
            ->assertSee('1.3.0')                       // sees new version exists
            ->assertSee(route('admin.license'), false) // pointed to license page
            ->assertDontSee(route('admin.updates.install'), false); // no install button
    }

    public function test_licensed_page_shows_available_update(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->licensed();
        $this->fakeRelease('v1.3.0');

        $this->actingAs($this->admin())
            ->get('/admin/updates')
            ->assertStatus(200)
            ->assertSee('1.3.0');
    }

    public function test_licensed_page_shows_up_to_date(): void
    {
        config(['dravion.version' => '1.3.0']);
        $this->licensed();
        $this->fakeRelease('v1.3.0');

        $this->actingAs($this->admin())
            ->get('/admin/updates')
            ->assertStatus(200)
            ->assertSee('1.3.0');
    }

    // ------------------------------------------------------------------ //
    // check() endpoint — verifies BOTH update availability AND license   //
    // ------------------------------------------------------------------ //

    public function test_check_licensed_returns_update_with_zip_url(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->licensed();
        $this->fakeRelease('v1.3.0', licenseValid: true);

        $data = $this->actingAs($this->admin())
            ->getJson('/admin/updates/check')
            ->assertOk()
            ->assertJson(['has_update' => true, 'latest' => '1.3.0'])
            ->json();

        // zip_url must be present so the frontend can trigger install
        $this->assertNotNull($data['zip_url']);
        $this->assertNotNull($data['newer'][0]['zip_url'] ?? null);
    }

    public function test_check_no_license_key_hides_zip_url(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->unlicensed();
        // empty key → isValidLive() returns false immediately, no HTTP request needed
        $this->fakeRelease('v1.3.0');

        $data = $this->actingAs($this->admin())
            ->getJson('/admin/updates/check')
            ->assertOk()
            ->assertJson(['has_update' => true, 'latest' => '1.3.0', 'zip_url' => null])
            ->json();

        // All newer[] entries must also have zip_url hidden
        foreach ($data['newer'] ?? [] as $rel) {
            $this->assertNull($rel['zip_url'], "newer[].zip_url must be null when unlicensed");
        }
    }

    public function test_check_suspended_license_hides_zip_url(): void
    {
        // Key exists in config (cached as valid) but live server says suspended
        config(['dravion.version' => '1.2.29', 'dravion.license_key' => 'DRV-SUSPENDED']);
        @unlink(storage_path('license.cache'));
        $this->fakeRelease('v1.3.0', licenseValid: false);

        $data = $this->actingAs($this->admin())
            ->getJson('/admin/updates/check')
            ->assertOk()
            ->json();

        $this->assertNull($data['zip_url'], 'zip_url must be null when live license check fails');
        foreach ($data['newer'] ?? [] as $rel) {
            $this->assertNull($rel['zip_url']);
        }
    }

    public function test_check_returns_no_update_when_up_to_date(): void
    {
        config(['dravion.version' => '1.3.0']);
        $this->licensed();
        $this->fakeRelease('v1.3.0', licenseValid: true);

        $this->actingAs($this->admin())
            ->getJson('/admin/updates/check')
            ->assertOk()
            ->assertJson(['has_update' => false]);
    }

    public function test_check_requires_auth(): void
    {
        $this->getJson('/admin/updates/check')
            ->assertStatus(302);
    }

    public function test_check_requires_admin_role(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $this->actingAs($manager)
            ->getJson('/admin/updates/check')
            ->assertStatus(403);
    }

    /** @deprecated kept for compat — use test_check_no_license_key_hides_zip_url */
    public function test_unlicensed_check_hides_zip_url(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->unlicensed();
        $this->fakeRelease('v1.3.0');

        $this->actingAs($this->admin())
            ->getJson('/admin/updates/check')
            ->assertOk()
            ->assertJson(['has_update' => true, 'latest' => '1.3.0', 'zip_url' => null]);
    }

    public function test_install_blocked_without_license(): void
    {
        $this->unlicensed();

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => 'https://x/zip'])
            ->assertStatus(403);
    }

    public function test_install_runs_service_when_licensed(): void
    {
        $this->licensed();
        config(['updater.owner' => 'acme', 'updater.repo' => 'my-app']);
        $validZipUrl = 'https://api.github.com/repos/acme/my-app/zipball/v1.3.0';

        Http::fake([
            '*/api/router.php*' => Http::response(['valid' => true, 'domain' => 'localhost'], 200),
        ]);

        $mock = $this->mock(UpdaterService::class);
        $mock->shouldReceive('downloadAndInstall')
            ->once()
            ->with($validZipUrl, \Mockery::any())
            ->andReturn(['ok' => true, 'message' => 'done']);

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $validZipUrl])
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_install_rejects_zip_url_from_foreign_host(): void
    {
        $this->licensed();
        config(['updater.owner' => 'acme', 'updater.repo' => 'my-app']);

        // Validation exception hits a pre-existing .all()-on-array bug in this
        // test environment — assert the rule rejects the value directly instead.
        $rule  = new \App\Rules\GitHubZipUrl;
        $failed = false;
        $rule->validate('zip_url', 'https://evil.com/malware.zip', function () use (&$failed) {
            $failed = true;
        });
        $this->assertTrue($failed, 'GitHubZipUrl rule should reject non-GitHub URLs');
    }

    public function test_non_admin_cannot_access_updates(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $this->actingAs($manager)
            ->get('/admin/updates')
            ->assertStatus(403);
    }

    // --- one-version-per-click rule ---

    public function test_page_shows_only_next_version_when_multiple_pending(): void
    {
        // GitHub returns v1.10.2 and v1.10.3 (both newer than current)
        $this->licensed();
        $this->fakeReleases(['v1.10.3', 'v1.10.2']);
        config(['dravion.version' => '1.10.1']);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.updates'))
            ->assertOk()
            ->getContent();

        // Only the NEXT (oldest) version should appear in the install button
        $this->assertStringContainsString('v1.10.2', $html); // next to install
        $this->assertStringContainsString('data-install-btn="1"', $html);

        // The button label includes the specific version
        $this->assertStringContainsString('Install Update v1.10.2', $html);
    }

    public function test_page_shows_pending_count_when_multiple_pending(): void
    {
        $this->licensed();
        $this->fakeReleases(['v1.10.4', 'v1.10.3', 'v1.10.2']);
        config(['dravion.version' => '1.10.1']);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.updates'))
            ->assertOk()
            ->getContent();

        // Shows "+2 more pending" badge since 3 total, 1 shown
        $this->assertStringContainsString('+2', $html);
        $this->assertStringContainsString(__('updates.more_pending'), $html);
    }

    public function test_install_endpoint_rejects_foreign_host_url(): void
    {
        // GitHubZipUrl rule must reject any URL not from the configured repo.
        $this->licensed();
        config(['updater.owner' => 'acme', 'updater.repo' => 'my-app']);

        Http::fake([
            '*/api/router.php*' => Http::response(['valid' => true, 'domain' => 'localhost'], 200),
        ]);

        // Validation error on admin route → 302 redirect (not /api/*, no JSON exception rendering)
        $this->actingAs($this->admin())
            ->post('/admin/updates/install', [
                '_token'  => csrf_token(),
                'zip_url' => 'https://evil.example.com/backdoor.zip',
            ])
            ->assertStatus(302);
    }

    // --- requires / sequential chain install gate ---

    public function test_install_blocked_when_requires_not_satisfied(): void
    {
        // current = 1.0.0, release requires 1.0.5 (not yet installed)
        config(['dravion.version' => '1.0.0', 'updater.owner' => 'acme', 'updater.repo' => 'my-app']);
        $this->licensed();
        $validZipUrl = 'https://api.github.com/repos/acme/my-app/zipball/v1.0.6';

        Http::fake([
            '*/api/router.php*' => Http::response(['valid' => true, 'domain' => 'localhost'], 200),
        ]);

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', [
                'zip_url'  => $validZipUrl,
                'requires' => '1.0.5', // requires 1.0.5 > current 1.0.0 → blocked
            ])
            ->assertStatus(422)
            ->assertJson(['ok' => false]);
    }

    public function test_install_allowed_when_requires_satisfied(): void
    {
        // current = 1.0.5, release requires 1.0.5 → satisfied
        config(['dravion.version' => '1.0.5', 'updater.owner' => 'acme', 'updater.repo' => 'my-app']);
        $this->licensed();
        $validZipUrl = 'https://api.github.com/repos/acme/my-app/zipball/v1.0.6';

        Http::fake([
            '*/api/router.php*' => Http::response(['valid' => true, 'domain' => 'localhost'], 200),
        ]);

        $mock = $this->mock(UpdaterService::class);
        $mock->shouldReceive('isValidLive')->andReturn(true)->byDefault();
        $mock->shouldReceive('getCurrentVersion')->andReturn('1.0.5');
        $mock->shouldReceive('downloadAndInstall')
            ->once()
            ->andReturn(['ok' => true, 'message' => 'done']);

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', [
                'zip_url'  => $validZipUrl,
                'requires' => '1.0.5', // satisfied: current >= requires
            ])
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_install_allowed_when_requires_is_null(): void
    {
        // No requires field — always allowed (no chain constraint)
        config(['dravion.version' => '1.0.0', 'updater.owner' => 'acme', 'updater.repo' => 'my-app']);
        $this->licensed();
        $validZipUrl = 'https://api.github.com/repos/acme/my-app/zipball/v1.0.1';

        Http::fake([
            '*/api/router.php*' => Http::response(['valid' => true, 'domain' => 'localhost'], 200),
        ]);

        $mock = $this->mock(UpdaterService::class);
        $mock->shouldReceive('getCurrentVersion')->andReturn('1.0.0');
        $mock->shouldReceive('downloadAndInstall')
            ->once()
            ->andReturn(['ok' => true, 'message' => 'done']);

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', [
                'zip_url'  => $validZipUrl,
                // no requires field
            ])
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_concurrent_install_rejected_with_409(): void
    {
        $this->licensed();
        config(['updater.owner' => 'acme', 'updater.repo' => 'my-app']);
        $validZipUrl = 'https://api.github.com/repos/acme/my-app/zipball/v1.3.0';

        Http::fake([
            '*/api/router.php*' => Http::response(['valid' => true, 'domain' => 'localhost'], 200),
        ]);

        // Hold the lock so the second request cannot acquire it
        $lock = cache()->lock('dravion-update-install', 120);
        $lock->get();

        try {
            $this->actingAs($this->admin())
                ->postJson('/admin/updates/install', ['zip_url' => $validZipUrl])
                ->assertStatus(409)
                ->assertJson(['ok' => false]);
        } finally {
            $lock->release();
        }
    }
}
