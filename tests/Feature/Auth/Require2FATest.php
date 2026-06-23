<?php

namespace Tests\Feature\Auth;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class Require2FATest extends TestCase
{
    use RefreshDatabase;

    private function enableRequire2FA(): void
    {
        Setting::set('require_2fa', '1');
        Setting::flushCache();
    }

    public function test_user_without_2fa_redirected_to_setup_when_required(): void
    {
        $this->enableRequire2FA();
        $user = User::factory()->create(['two_factor_confirmed_at' => null]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('profile.two-factor'));
    }

    public function test_user_with_2fa_can_access_dashboard_when_required(): void
    {
        $this->enableRequire2FA();
        $user = User::factory()->create([
            'two_factor_secret'       => (new Google2FA())->generateSecretKey(),
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_enforcement_disabled_when_setting_off(): void
    {
        Setting::set('require_2fa', '0');
        Setting::flushCache();
        $user = User::factory()->create(['two_factor_confirmed_at' => null]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_2fa_setup_route_exempt_from_enforcement(): void
    {
        // profile.two-factor must NOT redirect back to itself (infinite loop)
        $this->enableRequire2FA();
        $user = User::factory()->create([
            'two_factor_secret'       => (new Google2FA())->generateSecretKey(),
            'two_factor_confirmed_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('profile.two-factor'));
        // Middleware exempts the route — so it must NOT redirect to profile.two-factor again
        $this->assertNotSame(route('profile.two-factor'), $response->headers->get('Location'));
    }

    public function test_admin_without_2fa_redirected_when_required(): void
    {
        $this->enableRequire2FA();
        $admin = User::factory()->create(['two_factor_confirmed_at' => null]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('profile.two-factor'));
    }
}
