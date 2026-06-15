<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_change_password_from_profile(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPass123!')]);
        $user->assignRole('user');

        $this->actingAs($user)
            ->put('/profile/password', [
                'current_password'      => 'OldPass123!',
                'password'              => 'NewPass456!',
                'password_confirmation' => 'NewPass456!',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('NewPass456!', $user->fresh()->password));
    }

    public function test_change_password_requires_correct_current(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPass123!')]);
        $user->assignRole('user');

        $this->actingAs($user)
            ->put('/profile/password', [
                'current_password'      => 'WrongPass!',
                'password'              => 'NewPass456!',
                'password_confirmation' => 'NewPass456!',
            ])
            ->assertSessionHasErrors('current_password');
    }

    public function test_forgot_password_page_loads(): void
    {
        $this->get('/forgot-password')->assertStatus(200);
    }

    public function test_forgot_password_sends_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_page_loads_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->get('/reset-password/' . $notification->token);
            $response->assertStatus(200);
            return true;
        });
    }

    public function test_reset_password_updates_password(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $this->post('/reset-password', [
                'token'                 => $notification->token,
                'email'                 => $user->email,
                'password'              => 'NewResetPass1!',
                'password_confirmation' => 'NewResetPass1!',
            ])->assertRedirect('/login');

            $this->assertTrue(Hash::check('NewResetPass1!', $user->fresh()->password));
            return true;
        });
    }
}
