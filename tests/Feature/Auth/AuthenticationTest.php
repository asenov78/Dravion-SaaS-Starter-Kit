<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_failed_login_is_logged_to_activity(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name'   => 'auth',
            'event'      => 'login_failed',
            'subject_id' => $user->id,
        ]);
    }

    public function test_failed_login_for_unknown_email_is_logged(): void
    {
        $this->post('/login', [
            'email'    => 'nobody@example.com',
            'password' => 'wrong',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'auth',
            'event'    => 'login_failed',
        ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
