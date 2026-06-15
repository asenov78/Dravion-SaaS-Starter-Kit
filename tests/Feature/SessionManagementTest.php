<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SessionManagementTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $u = User::factory()->create(['email_verified_at' => now()]);
        $u->assignRole('user');
        return $u;
    }

    // --- Sessions page ---

    public function test_sessions_page_renders(): void
    {
        $this->actingAs($this->user())
            ->get('/sessions')
            ->assertStatus(200);
    }

    public function test_guest_redirected_from_sessions_page(): void
    {
        $this->get('/sessions')->assertRedirect('/login');
    }

    // --- Logout other devices ---

    public function test_logout_other_devices_requires_password(): void
    {
        $this->actingAs($this->user())
            ->post('/sessions/logout-others', ['password' => ''])
            ->assertSessionHasErrors('password');
    }

    public function test_logout_other_devices_wrong_password_fails(): void
    {
        $this->actingAs($this->user())
            ->post('/sessions/logout-others', ['password' => 'wrong-password'])
            ->assertSessionHasErrors('password');
    }

    public function test_logout_other_devices_correct_password_succeeds(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->post('/sessions/logout-others', ['password' => 'password'])
            ->assertRedirect('/sessions');
    }
}
