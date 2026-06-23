<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    // --- Challenge (login gate) ---

    public function test_challenge_redirects_to_login_without_session(): void
    {
        $response = $this->get('/two-factor/challenge');

        $response->assertRedirect(route('login'));
    }

    public function test_challenge_page_renders_with_valid_session(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession(['2fa_user_id' => $user->id])
            ->get('/two-factor/challenge');

        $response->assertOk();
        $response->assertViewIs('auth.two-factor.challenge');
    }

    public function test_verify_rejects_invalid_code(): void
    {
        $google2fa = new Google2FA();
        $secret    = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'two_factor_secret'       => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->withSession(['2fa_user_id' => $user->id])
            ->post('/two-factor/challenge', ['code' => '000000']);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_verify_logs_in_user_with_valid_code(): void
    {
        $google2fa = new Google2FA();
        $secret    = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'two_factor_secret'       => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $validCode = $google2fa->getCurrentOtp($secret);

        $response = $this->withSession(['2fa_user_id' => $user->id])
            ->post('/two-factor/challenge', ['code' => $validCode]);

        $this->assertAuthenticatedAs($user);
        $this->assertNull(session('2fa_user_id'));
    }

    // --- Setup embedded in profile ---

    private function adminUser(array $attrs = []): \App\Models\User
    {
        $u = User::factory()->create(array_merge(['email_verified_at' => now()], $attrs));
        $u->assignRole('admin');
        return $u;
    }

    public function test_profile_page_shows_2fa_setup_section_when_not_enabled(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('admin.ui.profile'))
            ->assertOk()
            ->assertSee(__('auth.2fa_enable'));
    }

    public function test_profile_page_shows_2fa_enabled_status_when_active(): void
    {
        $user = $this->adminUser([
            'two_factor_secret'       => (new Google2FA())->generateSecretKey(),
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('admin.ui.profile'))
            ->assertOk()
            ->assertSee(__('auth.2fa_enabled_badge'));
    }

    public function test_profile_two_factor_route_redirects_to_profile(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('profile.two-factor'))
            ->assertRedirect(route('admin.ui.profile'));
    }

    public function test_confirm_rejects_invalid_code(): void
    {
        $secret = (new Google2FA())->generateSecretKey();
        $user   = User::factory()->create([
            'email_verified_at'  => now(),
            'two_factor_secret'  => $secret,
        ]);

        $this->actingAs($user)->post('/profile/two-factor', ['code' => '000000']);

        $this->assertNull($user->fresh()->two_factor_confirmed_at);
    }

    public function test_confirm_enables_2fa_with_valid_code(): void
    {
        $google2fa = new Google2FA();
        $secret    = $google2fa->generateSecretKey();
        $user      = User::factory()->create([
            'email_verified_at'  => now(),
            'two_factor_secret'  => $secret,
        ]);

        $validCode = $google2fa->getCurrentOtp($secret);

        $this->actingAs($user)->post('/profile/two-factor', ['code' => $validCode]);

        $this->assertNotNull($user->fresh()->two_factor_confirmed_at);
    }

    // --- Disable ---

    public function test_disable_rejects_wrong_password(): void
    {
        $secret = (new Google2FA())->generateSecretKey();
        $user   = User::factory()->create([
            'email_verified_at'       => now(),
            'two_factor_secret'       => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($user)->delete('/profile/two-factor', ['password' => 'wrong-password']);

        $this->assertNotNull($user->fresh()->two_factor_confirmed_at);
    }

    public function test_disable_clears_2fa_with_correct_password(): void
    {
        $secret = (new Google2FA())->generateSecretKey();
        $user   = User::factory()->create([
            'email_verified_at'       => now(),
            'two_factor_secret'       => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($user)->delete('/profile/two-factor', ['password' => 'password']);

        $fresh = $user->fresh();
        $this->assertNull($fresh->two_factor_secret);
        $this->assertNull($fresh->two_factor_confirmed_at);
    }

    // --- Login gate (LoginController) ---

    public function test_login_redirects_to_2fa_challenge_when_enabled(): void
    {
        $secret = (new Google2FA())->generateSecretKey();
        $user   = User::factory()->create([
            'password'                => bcrypt('password'),
            'two_factor_secret'       => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('two-factor.challenge'));
        $this->assertEquals($user->id, session('2fa_user_id'));
        $this->assertGuest();
    }
}
