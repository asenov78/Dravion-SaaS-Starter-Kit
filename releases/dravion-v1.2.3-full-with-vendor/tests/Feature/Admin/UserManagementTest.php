<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_list_users(): void
    {
        User::factory(3)->create()->each(fn ($u) => $u->assignRole('user'));

        $this->actingAs($this->admin)
            ->get('/admin/users')
            ->assertStatus(200)
            ->assertSee('users');
    }

    public function test_admin_can_create_user(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/users', [
                'name'     => 'New User',
                'email'    => 'new@test.com',
                'password' => 'password123',
                'role'     => 'user',
            ])
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', ['email' => 'new@test.com']);
        $user = User::where('email', 'new@test.com')->first();
        $this->assertTrue($user->hasRole('user'));
    }

    public function test_admin_can_edit_user(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $user->assignRole('user');

        $this->actingAs($this->admin)
            ->put("/admin/users/{$user->id}", [
                'name'  => 'New Name',
                'email' => $user->email,
                'role'  => 'editor',
            ])
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
        $user->refresh();
        $this->assertTrue($user->hasRole('editor'));
    }

    public function test_admin_can_suspend_user(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('user');

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$user->id}/suspend")
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'suspended']);
    }

    public function test_admin_can_activate_suspended_user(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);
        $user->assignRole('user');

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$user->id}/activate")
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'active']);
    }

    public function test_admin_cannot_suspend_themselves(): void
    {
        $this->actingAs($this->admin)
            ->patch("/admin/users/{$this->admin->id}/suspend")
            ->assertStatus(403);
    }

    public function test_manager_can_list_and_suspend_users(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('user');

        $this->actingAs($manager)
            ->patch("/admin/users/{$user->id}/suspend")
            ->assertRedirect('/admin/users');
    }

    public function test_regular_user_cannot_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertStatus(403);
    }
}
