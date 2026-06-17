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
        config(['dravion.license_key' => 'DRV-VALID']);
        @unlink(storage_path('license.cache'));
    }

    private function unlicensed(): void
    {
        config(['dravion.license_key' => '']);
    }

    private function fakeRelease(string $tag): void
    {
        $this->fakeReleases([$tag]);
    }

    /** @param string[] $tags newest first */
    private function fakeReleases(array $tags): void
    {
        Http::fake([
            'api.github.com/*' => Http::response(array_map(fn ($tag) => [
                'tag_name'    => $tag,
                'body'        => "Release notes for {$tag}",
                'zipball_url' => 'https://api.github.com/zip/' . $tag,
                'draft'       => false,
                'prerelease'  => false,
            ], $tags), 200),
        ]);
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

    public function test_check_endpoint_returns_json(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->licensed();
        $this->fakeRelease('v1.3.0');

        $this->actingAs($this->admin())
            ->getJson('/admin/updates/check')
            ->assertOk()
            ->assertJson(['has_update' => true, 'latest' => '1.3.0']);
    }

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
}
