<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckLicenseTest extends TestCase
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

    private function fakeLicense(bool $valid): void
    {
        $body   = $valid
            ? ['valid' => true,  'license_key' => 'DRV-VALID', 'domain' => 'localhost']
            : ['valid' => false, 'error' => 'License not found'];
        $status = $valid ? 200 : 403;
        Http::fake(['*/api/router.php*' => Http::response($body, $status)]);
    }

    // --- route access ---

    public function test_unauthenticated_cannot_post(): void
    {
        $this->post(route('admin.updates.check-license'))
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_post(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $this->actingAs($manager)
            ->post(route('admin.updates.check-license'))
            ->assertStatus(403);
    }

    // --- cache clearing ---

    public function test_clears_license_cache_before_checking(): void
    {
        // Stale cache says valid=false
        file_put_contents(storage_path('license.cache'), json_encode([
            'valid'      => false,
            'checked_at' => time() - 3600,
        ]));

        config(['dravion.license_key' => 'DRV-VALID']);
        $this->fakeLicense(valid: true);

        // Even though the stale cache says invalid, the live check should succeed
        $this->actingAs($this->admin())
            ->post(route('admin.updates.check-license'))
            ->assertRedirect(route('admin.updates'))
            ->assertSessionHas('success');
    }

    // --- redirect with success when license is active ---

    public function test_valid_license_redirects_with_success_flash(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->fakeLicense(valid: true);

        $this->actingAs($this->admin())
            ->post(route('admin.updates.check-license'))
            ->assertRedirect(route('admin.updates'))
            ->assertSessionHas('success', __('flash.license_verified'));
    }

    // --- redirect with warning when license is invalid ---

    public function test_invalid_license_redirects_with_warning_flash(): void
    {
        config(['dravion.license_key' => 'DRV-SUSPENDED']);
        $this->fakeLicense(valid: false);

        $this->actingAs($this->admin())
            ->post(route('admin.updates.check-license'))
            ->assertRedirect(route('admin.updates'))
            ->assertSessionHas('license_warning', __('flash.license_not_found'));
    }

    public function test_missing_license_key_redirects_with_warning_flash(): void
    {
        config(['dravion.license_key' => '']);

        $this->actingAs($this->admin())
            ->post(route('admin.updates.check-license'))
            ->assertRedirect(route('admin.updates'))
            ->assertSessionHas('license_warning');
    }

    // --- blade button exists ---

    public function test_updates_page_shows_check_license_button_when_unlicensed(): void
    {
        config(['dravion.license_key' => '']);
        Http::fake(['api.github.com/*' => Http::response([[
            'tag_name'    => 'v99.0.0',
            'body'        => '',
            'zipball_url' => 'https://api.github.com/zip/v99.0.0',
            'draft'       => false,
            'prerelease'  => false,
        ]], 200)]);
        config(['updater.owner' => 'o', 'updater.repo' => 'r']);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.updates'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(route('admin.updates.check-license'), $html);
        $this->assertStringContainsString(__('updates.check_license'), $html);
    }
}
