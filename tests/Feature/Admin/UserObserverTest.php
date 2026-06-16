<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserObserverTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_user_creation_is_logged(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', [
            'name'     => 'New User',
            'email'    => 'new@example.com',
            'password' => 'password123',
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'users',
            'event'    => 'created',
        ]);
    }

    public function test_user_update_is_logged(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $user->assignRole('user');

        $this->actingAs($this->admin)->put("/admin/users/{$user->id}", [
            'name'   => 'New Name',
            'email'  => $user->email,
            'role'   => 'user',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name'   => 'users',
            'event'      => 'updated',
            'subject_id' => $user->id,
        ]);
    }

    public function test_user_deletion_is_logged(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($this->admin)->delete("/admin/users/{$user->id}");

        $this->assertDatabaseHas('activity_log', [
            'log_name'   => 'users',
            'event'      => 'deleted',
            'subject_id' => $user->id,
        ]);
    }

    public function test_user_restore_is_logged(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $user->delete();

        $this->actingAs($this->admin)->patch("/admin/users/{$user->id}/restore");

        $this->assertDatabaseHas('activity_log', [
            'log_name'   => 'users',
            'event'      => 'restored',
            'subject_id' => $user->id,
        ]);
    }
}
