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
        $response = $this->actingAs($this->admin)
            ->post('/admin/users', [
                'name'     => 'New User',
                'email'    => 'new@test.com',
                'password' => 'password123',
                'role'     => 'user',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'new@test.com']);
        $user = User::where('email', 'new@test.com')->first();
        $response->assertRedirect(route('admin.users.edit', $user));
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
            ->assertRedirect("/admin/users/{$user->id}/edit");

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

    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($this->admin)
            ->delete("/admin/users/{$user->id}")
            ->assertRedirect('/admin/users');

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $this->actingAs($this->admin)
            ->delete("/admin/users/{$this->admin->id}")
            ->assertStatus(403);
    }

    public function test_deleted_user_not_in_list(): void
    {
        $user = User::factory()->create(['name' => 'ToDelete User']);
        $user->assignRole('user');
        $user->delete();

        $this->actingAs($this->admin)
            ->get('/admin/users')
            ->assertStatus(200)
            ->assertDontSee('ToDelete User');
    }

    public function test_suspend_returns_json_for_ajax_request(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('user');

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$user->id}/suspend", [], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'suspended']);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'suspended']);
    }

    public function test_activate_returns_json_for_ajax_request(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);
        $user->assignRole('user');

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$user->id}/activate", [], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'active']);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'active']);
    }

    public function test_delete_returns_json_for_ajax_request(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($this->admin)
            ->delete("/admin/users/{$user->id}", [], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_manager_cannot_delete_user(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($manager)
            ->delete("/admin/users/{$user->id}")
            ->assertStatus(403);
    }

    public function test_create_page_has_all_sections(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/users/create')
            ->assertStatus(200)
            ->assertSee('Personal')
            ->assertSee('Address')
            ->assertSee('Social')
            ->assertSee('phone')
            ->assertSee('country');
    }

    public function test_admin_can_create_user_with_extra_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/users', [
                'name'     => 'Full User',
                'email'    => 'full@test.com',
                'password' => 'password123',
                'role'     => 'user',
                'phone'    => '+359888123456',
                'bio'      => 'Test bio',
                'country'  => 'Bulgaria',
            ]);

        $created = User::where('email', 'full@test.com')->first();
        $response->assertRedirect(route('admin.users.edit', $created));

        $this->assertDatabaseHas('users', [
            'email'   => 'full@test.com',
            'phone'   => '+359888123456',
            'country' => 'Bulgaria',
        ]);
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

    // --- Restore ---

    public function test_admin_can_restore_deleted_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$user->id}/restore")
            ->assertRedirect('/admin/users');

        $this->assertNotSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_restore_returns_json_for_ajax_request(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $user->delete();

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$user->id}/restore", [], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertNotSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_trashed_users_visible_in_trash_tab(): void
    {
        $user = User::factory()->create(['name' => 'Deleted Person']);
        $user->assignRole('user');
        $user->delete();

        $this->actingAs($this->admin)
            ->get('/admin/users?trashed=1')
            ->assertStatus(200)
            ->assertSee('Deleted Person');
    }

    public function test_search_works_in_trash_tab(): void
    {
        $match   = User::factory()->create(['name' => 'Deleted Alpha']);
        $nomatch = User::factory()->create(['name' => 'Deleted Beta']);
        $match->assignRole('user');
        $nomatch->assignRole('user');
        $match->delete();
        $nomatch->delete();

        $this->actingAs($this->admin)
            ->get('/admin/users?trashed=1&search=Alpha')
            ->assertStatus(200)
            ->assertSee('Deleted Alpha')
            ->assertDontSee('Deleted Beta');
    }

    public function test_trashed_users_not_visible_in_normal_list(): void
    {
        $user = User::factory()->create(['name' => 'Deleted Person']);
        $user->assignRole('user');
        $user->delete();

        $this->actingAs($this->admin)
            ->get('/admin/users')
            ->assertStatus(200)
            ->assertDontSee('Deleted Person');
    }

    // --- Filters ---

    public function test_filter_by_role(): void
    {
        $admin2 = User::factory()->create(['name' => 'Second Admin']);
        $admin2->assignRole('admin');
        $editor = User::factory()->create(['name' => 'Editor Person']);
        $editor->assignRole('editor');

        $this->actingAs($this->admin)
            ->get('/admin/users?role=editor')
            ->assertStatus(200)
            ->assertSee('Editor Person')
            ->assertDontSee('Second Admin');
    }

    public function test_filter_by_status(): void
    {
        $active    = User::factory()->create(['name' => 'Active Person',    'status' => 'active']);
        $suspended = User::factory()->create(['name' => 'Suspended Person', 'status' => 'suspended']);
        $active->assignRole('user');
        $suspended->assignRole('user');

        $this->actingAs($this->admin)
            ->get('/admin/users?status=suspended')
            ->assertStatus(200)
            ->assertSee('Suspended Person')
            ->assertDontSee('Active Person');
    }

    // --- Export CSV ---

    public function test_admin_can_export_users_csv(): void
    {
        User::factory()->create(['name' => 'Export User', 'email' => 'export@test.com'])->assignRole('user');

        $response = $this->actingAs($this->admin)
            ->get('/admin/users/export')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->assertStringContainsString('export@test.com', $response->streamedContent());
    }
}
