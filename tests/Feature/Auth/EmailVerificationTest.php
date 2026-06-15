<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function unverifiedUser(): User
    {
        $user = User::factory()->unverified()->create();
        $user->assignRole('user');
        return $user;
    }

    private function verifiedUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        return $user;
    }

    // --- Registration redirects to verify notice ---

    public function test_register_redirects_to_email_verify_notice(): void
    {
        $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect('/email/verify');
    }

    public function test_registered_user_has_null_email_verified_at(): void
    {
        $this->post('/register', [
            'name'                  => 'New User',
            'email'                 => 'new@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'email'             => 'new@example.com',
            'email_verified_at' => null,
        ]);
    }

    // --- Verify notice page ---

    public function test_unverified_user_sees_verify_notice(): void
    {
        $user = $this->unverifiedUser();

        $this->actingAs($user)
            ->get('/email/verify')
            ->assertStatus(200);
    }

    public function test_verified_user_redirected_from_notice(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)
            ->get('/email/verify')
            ->assertRedirect('/dashboard');
    }

    // --- Unverified user blocked from dashboard ---

    public function test_unverified_user_redirected_to_verify_from_dashboard(): void
    {
        $user = $this->unverifiedUser();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect('/email/verify');
    }

    public function test_verified_user_can_access_dashboard(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(200);
    }

    // --- Resend verification email ---

    public function test_resend_sends_verification_notification(): void
    {
        $user = $this->unverifiedUser();

        $this->actingAs($user)
            ->post('/email/verification-notification')
            ->assertRedirect('/email/verify');
    }

    // --- Verify via signed URL ---

    public function test_valid_signed_url_verifies_user(): void
    {
        Event::fake();
        $user = $this->unverifiedUser();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect('/dashboard');

        Event::assertDispatched(Verified::class);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_invalid_hash_is_rejected(): void
    {
        $user = $this->unverifiedUser();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'badhash']
        );

        $this->actingAs($user)
            ->get($url)
            ->assertStatus(403);
    }

    public function test_already_verified_user_is_redirected(): void
    {
        $user = $this->verifiedUser();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect('/dashboard');
    }
}
