<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_sessions_page_requires_auth(): void
    {
        $this->get('/sessions')->assertRedirect('/login');
    }

    public function test_sessions_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get('/sessions')
            ->assertStatus(200);
    }

    public function test_logout_others_requires_password(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->post('/sessions/logout-others', [])
            ->assertSessionHasErrors('password');
    }

    public function test_logout_others_rejects_wrong_password(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->post('/sessions/logout-others', ['password' => 'wrongpassword'])
            ->assertSessionHasErrors('password');
    }

    public function test_logout_others_succeeds_with_correct_password(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->post('/sessions/logout-others', ['password' => 'password'])
            ->assertRedirect('/sessions');
    }
}
