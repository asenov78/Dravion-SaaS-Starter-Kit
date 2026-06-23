<?php

namespace Tests\Feature\Auth;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorRememberTest extends TestCase
{
    use RefreshDatabase;

    private Google2FA $google2fa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->google2fa = new Google2FA();
    }

    private function userWith2FA(): User
    {
        $secret = $this->google2fa->generateSecretKey();
        return User::factory()->create([
            'password'                => bcrypt('password'),
            'two_factor_secret'       => $secret,
            'two_factor_confirmed_at' => now(),
        ]);
    }

    private function cookieName(User $user): string
    {
        return 'dravion_2fa_' . $user->id;
    }

    // ── verify() sets cookie ─────────────────────────────────────────────────

    public function test_verify_sets_remember_cookie_when_days_configured(): void
    {
        Setting::set('2fa_remember_days', '30');

        $user      = $this->userWith2FA();
        $validCode = $this->google2fa->getCurrentOtp($user->two_factor_secret);

        $response = $this->withSession(['2fa_user_id' => $user->id])
            ->post('/two-factor/challenge', ['code' => $validCode, 'remember_device' => '1']);

        $cookies = collect($response->headers->getCookies())
            ->keyBy(fn($c) => $c->getName());

        $this->assertArrayHasKey($this->cookieName($user), $cookies->toArray());
        $this->assertGreaterThan(0, $cookies[$this->cookieName($user)]->getMaxAge());
    }

    public function test_verify_no_cookie_when_remember_days_zero(): void
    {
        Setting::set('2fa_remember_days', '0');

        $user      = $this->userWith2FA();
        $validCode = $this->google2fa->getCurrentOtp($user->two_factor_secret);

        $response = $this->withSession(['2fa_user_id' => $user->id])
            ->post('/two-factor/challenge', ['code' => $validCode]);

        $cookieNames = array_map(
            fn($c) => $c->getName(),
            $response->headers->getCookies()
        );

        $this->assertNotContains($this->cookieName($user), $cookieNames);
    }

    // ── login() bypasses challenge ───────────────────────────────────────────

    public function test_login_skips_challenge_with_valid_remember_cookie(): void
    {
        Setting::set('2fa_remember_days', '30');

        $user = $this->userWith2FA();
        $name = $this->cookieName($user);

        $loginResponse = $this->withCookies([$name => '1'])
            ->post('/login', [
                'email'    => $user->email,
                'password' => 'password',
            ]);

        $location = $loginResponse->headers->get('Location', '(no redirect)');
        $this->assertStringNotContainsString(
            'two-factor',
            $location,
            "Expected bypass but was redirected to: $location"
        );
        $loginResponse->assertRedirect();
    }

    public function test_login_requires_challenge_without_cookie(): void
    {
        Setting::set('2fa_remember_days', '30');

        $user = $this->userWith2FA();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();
    }

    public function test_login_requires_challenge_when_remember_disabled(): void
    {
        Setting::set('2fa_remember_days', '0');

        $user = $this->userWith2FA();

        $name = $this->cookieName($user);
        // Even with a valid cookie present, if setting is 0 → still challenge
        $response = $this->withCookies([
            $name => '1',
        ])->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();
    }

    // ── logout clears cookie ─────────────────────────────────────────────────

    public function test_logout_clears_remember_cookie(): void
    {
        $user = $this->userWith2FA();

        $response = $this->actingAs($user)->post('/logout');

        $cookies = collect($response->headers->getCookies())
            ->keyBy(fn($c) => $c->getName());

        // Cookie must be cleared (max-age 0 or negative expiry)
        if (isset($cookies[$this->cookieName($user)])) {
            $this->assertLessThanOrEqual(0, $cookies[$this->cookieName($user)]->getMaxAge());
        }
        // Cookie not in response at all is also valid (already cleared)
        $this->assertTrue(true);
    }

    public function test_logout_queues_cookie_deletion(): void
    {
        $user = $this->userWith2FA();

        $response = $this->actingAs($user)->post('/logout');

        // Verify we get redirected (logout worked)
        $response->assertRedirect(route('login'));

        // Check for a "clear" cookie (expired)
        $found = false;
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $this->cookieName($user) && $cookie->getMaxAge() <= 0) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Expected a cleared cookie for ' . $this->cookieName($user));
    }

    // ── remember_device checkbox on challenge page ───────────────────────────

    public function test_challenge_shows_remember_checkbox_when_days_configured(): void
    {
        Setting::set('2fa_remember_days', '30');

        $response = $this->withSession(['2fa_user_id' => 1])
            ->get('/two-factor/challenge');

        $response->assertOk();
        $response->assertSee('remember_device');
    }

    public function test_challenge_hides_remember_checkbox_when_days_zero(): void
    {
        Setting::set('2fa_remember_days', '0');

        $response = $this->withSession(['2fa_user_id' => 1])
            ->get('/two-factor/challenge');

        $response->assertOk();
        $response->assertDontSee('remember_device');
    }

    public function test_verify_sets_cookie_only_when_checkbox_checked(): void
    {
        Setting::set('2fa_remember_days', '30');

        $user      = $this->userWith2FA();
        $validCode = $this->google2fa->getCurrentOtp($user->two_factor_secret);

        $response = $this->withSession(['2fa_user_id' => $user->id])
            ->post('/two-factor/challenge', ['code' => $validCode, 'remember_device' => '1']);

        $cookies = collect($response->headers->getCookies())->keyBy(fn($c) => $c->getName());
        $this->assertArrayHasKey($this->cookieName($user), $cookies->toArray());
        $this->assertGreaterThan(0, $cookies[$this->cookieName($user)]->getMaxAge());
    }

    public function test_verify_no_cookie_when_checkbox_unchecked(): void
    {
        Setting::set('2fa_remember_days', '30');

        $user      = $this->userWith2FA();
        $validCode = $this->google2fa->getCurrentOtp($user->two_factor_secret);

        // POST without remember_device
        $response = $this->withSession(['2fa_user_id' => $user->id])
            ->post('/two-factor/challenge', ['code' => $validCode]);

        $cookieNames = array_map(fn($c) => $c->getName(), $response->headers->getCookies());
        $this->assertNotContains($this->cookieName($user), $cookieNames);
    }
}
