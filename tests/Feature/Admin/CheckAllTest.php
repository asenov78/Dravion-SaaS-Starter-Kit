<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckAllTest extends TestCase
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

    public function test_unauthenticated_cannot_post_check_all(): void
    {
        $this->post(route('admin.updates.check-all'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_post_check_all(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $this->actingAs($manager)
            ->post(route('admin.updates.check-all'))
            ->assertStatus(403);
    }

    public function test_check_all_deletes_license_cache(): void
    {
        // Write a valid-looking cache file (so LicenseCheck middleware skips verifyNow).
        $payload = json_encode(['valid' => false, 'checked_at' => time(), 'status' => 'invalid']);
        $sig     = hash_hmac('sha256', $payload, hash('sha256', config('app.key', 'fallback')));
        file_put_contents(storage_path('license.cache'), json_encode(['payload' => $payload, 'sig' => $sig]));

        $this->licensed();

        $this->actingAs($this->admin())
            ->post(route('admin.updates.check-all'))
            ->assertRedirect(route('admin.updates'));

        // checkAll() unlinks the file; after the controller runs the file must be gone
        $this->assertFileDoesNotExist(storage_path('license.cache'));
    }

    public function test_check_all_forgets_github_latest_version_cache(): void
    {
        Cache::put('github_latest_version', 'v1.0.0', 3600);

        $this->licensed();
        Http::fake(['*/api/router.php*' => Http::response(['valid' => true, 'domain' => 'localhost'], 200)]);

        $this->actingAs($this->admin())
            ->post(route('admin.updates.check-all'));

        $this->assertNull(Cache::get('github_latest_version'));
    }

    public function test_check_all_redirects_to_updates_with_success_flash(): void
    {
        $this->licensed();
        Http::fake(['*/api/router.php*' => Http::response(['valid' => true, 'domain' => 'localhost'], 200)]);

        $this->actingAs($this->admin())
            ->post(route('admin.updates.check-all'))
            ->assertRedirect(route('admin.updates'))
            ->assertSessionHas('success', __('flash.update_check_done'));
    }

    public function test_updates_page_caches_github_latest_version_on_load(): void
    {
        // When index() successfully fetches a GitHub release, it writes the
        // latest version to Cache so the sidebar badge can read it.
        $this->licensed();
        Cache::forget('github_latest_version');

        Http::fake([
            'api.github.com/*' => Http::response([[
                'tag_name'    => 'v9.9.9',
                'body'        => 'notes',
                'zipball_url' => 'https://api.github.com/repos/o/r/zipball/v9.9.9',
                'draft'       => false,
                'prerelease'  => false,
            ]], 200),
            '*/api/router.php*' => Http::response(['valid' => true, 'domain' => 'localhost'], 200),
        ]);
        config(['updater.owner' => 'o', 'updater.repo' => 'r']);

        $this->actingAs($this->admin())
            ->get(route('admin.updates'))
            ->assertOk();

        $this->assertSame('9.9.9', Cache::get('github_latest_version'));
    }

    public function test_check_again_button_is_a_post_form_not_an_anchor(): void
    {
        $this->licensed();
        Http::fake([
            'api.github.com/*' => Http::response([], 200),
            '*/api/router.php*' => Http::response(['valid' => true, 'domain' => 'localhost'], 200),
        ]);
        config(['updater.owner' => 'o', 'updater.repo' => 'r', 'dravion.version' => '1.0.0']);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.updates'))
            ->assertOk()
            ->getContent();

        // Must be a form POST to check-all, not a plain anchor link
        $this->assertStringContainsString('action="' . route('admin.updates.check-all') . '"', $html);
        $this->assertStringContainsString('method="POST"', $html);
    }
}
