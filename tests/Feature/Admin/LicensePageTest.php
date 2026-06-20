<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LicensePageTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        return $user;
    }

    public function test_admin_license_route_redirects_to_updates(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.license'))
            ->assertRedirect(route('admin.updates'));
    }

    public function test_non_admin_cannot_view_license_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('admin.license'))
            ->assertStatus(403);
    }

    public function test_updates_page_shows_masked_key_when_set(): void
    {
        config(['dravion.license_key' => 'DRV-ABCDEF123456789012345678']);

        $this->actingAs($this->admin())
            ->get(route('admin.updates'))
            ->assertSee('DRV-****');
    }

    public function test_updates_page_shows_unlicensed_when_no_key(): void
    {
        config(['dravion.license_key' => '']);

        $this->actingAs($this->admin())
            ->get(route('admin.updates'))
            ->assertSee('No license key configured')
            ->assertDontSee('Licensed');
    }

    public function test_updates_page_shows_invalid_status_when_warning_in_session(): void
    {
        config(['dravion.license_key' => 'DRV-SOMEKEY']);

        $this->actingAs($this->admin())
            ->withSession(['license_warning' => 'License invalid.'])
            ->get(route('admin.updates'))
            ->assertSee('Invalid')
            ->assertDontSee('Licensed');
    }

    public function test_submitting_valid_license_key_activates_and_redirects_with_success(): void
    {
        Http::fake([
            '*/api/router.php*' => Http::response(['license_key' => 'DRV-NEWKEY123', 'domain' => 'localhost'], 200),
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.license.update'), ['license_key' => 'PURCHASE-CODE-123'])
            ->assertRedirect(route('admin.license'))
            ->assertSessionHas('success');
    }

    public function test_activation_clears_license_cache(): void
    {
        $cacheFile = storage_path('license.cache');
        file_put_contents($cacheFile, json_encode(['valid' => false, 'checked_at' => time()]));

        Http::fake([
            '*/api/router.php*' => Http::response(['license_key' => 'DRV-NEWKEY123', 'domain' => 'localhost'], 200),
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.license.update'), ['license_key' => 'PURCHASE-CODE-123']);

        $this->assertFileDoesNotExist($cacheFile);
    }

    public function test_submitting_invalid_license_key_shows_error(): void
    {
        Http::fake([
            '*/api/router.php*' => Http::response(['error' => 'Invalid purchase code.'], 422),
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.license.update'), ['license_key' => 'INVALID-CODE'])
            ->assertRedirect(route('admin.license'))
            ->assertSessionHasErrors('license_key');
    }

    public function test_remove_license_clears_key_and_cache(): void
    {
        config(['dravion.license_key' => 'DRV-SOMEKEY']);
        file_put_contents(storage_path('license.cache'), json_encode(['valid' => true, 'checked_at' => time()]));

        $this->actingAs($this->admin())
            ->delete(route('admin.license.remove'))
            ->assertRedirect(route('admin.license'))
            ->assertSessionHas('success');

        $this->assertFileDoesNotExist(storage_path('license.cache'));
    }

    public function test_license_key_field_is_required(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.license.update'), ['license_key' => ''])
            ->assertSessionHasErrors('license_key');
    }

    public function test_successful_activation_clears_license_warning_from_session(): void
    {
        Http::fake([
            '*/api/router.php*' => Http::response(['license_key' => 'DRV-NEWKEY123', 'domain' => 'localhost'], 200),
        ]);

        $this->actingAs($this->admin())
            ->withSession(['license_warning' => 'No license key configured.'])
            ->post(route('admin.license.update'), ['license_key' => 'PURCHASE-CODE-123'])
            ->assertRedirect(route('admin.license'))
            ->assertSessionMissing('license_warning');
    }

    public function test_remove_license_shows_removed_flash_not_activated(): void
    {
        config(['dravion.license_key' => 'DRV-SOMEKEY']);

        $this->actingAs($this->admin())
            ->delete(route('admin.license.remove'))
            ->assertSessionHas('success', __('flash.license_removed'))
            ->assertSessionMissing('errors');

        $this->assertNotEquals(__('flash.license_activated'), __('flash.license_removed'));
    }

    public function test_activate_license_shows_activated_flash_not_removed(): void
    {
        Http::fake([
            '*/api/router.php*' => Http::response(['license_key' => 'DRV-NEWKEY', 'domain' => 'localhost'], 200),
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.license.update'), ['license_key' => 'PURCHASE-CODE-123'])
            ->assertSessionHas('success', __('flash.license_activated'))
            ->assertSessionMissing('errors');

        $this->assertNotEquals(__('flash.license_activated'), __('flash.license_removed'));
    }

    public function test_back_param_from_updates_page_redirects_to_updates(): void
    {
        config(['dravion.license_key' => 'DRV-SOMEKEY']);

        $this->actingAs($this->admin())
            ->delete(route('admin.license.remove'), ['_back' => route('admin.updates')])
            ->assertRedirect(route('admin.updates'));
    }

    public function test_back_param_unknown_value_redirects_to_license(): void
    {
        config(['dravion.license_key' => 'DRV-SOMEKEY']);

        $this->actingAs($this->admin())
            ->delete(route('admin.license.remove'), ['_back' => 'https://evil.com'])
            ->assertRedirect(route('admin.license'));
    }

    // --- Blur behaviour ---

    public function test_dashboard_is_blurred_when_no_license(): void
    {
        config(['dravion.license_key' => '']);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('filter:blur', $html);
        $this->assertStringContainsString('pointer-events:none', $html);
    }

    public function test_updates_page_is_NOT_blurred_when_no_license(): void
    {
        config(['dravion.license_key' => '']);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.updates'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('pointer-events:none', $html);
    }

    public function test_no_blur_when_license_present(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('pointer-events:none', $html);
    }

    public function test_flash_keys_are_distinct_in_both_locales(): void
    {
        $this->assertNotEquals(
            \Illuminate\Support\Facades\Lang::get('flash.license_activated', [], 'en'),
            \Illuminate\Support\Facades\Lang::get('flash.license_removed', [], 'en')
        );
        $this->assertNotEquals(
            \Illuminate\Support\Facades\Lang::get('flash.license_activated', [], 'bg'),
            \Illuminate\Support\Facades\Lang::get('flash.license_removed', [], 'bg')
        );
    }
}
