<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_roles_page_loads(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/roles')
            ->assertStatus(200)
            ->assertSee('admin')
            ->assertSee('manager');
    }

    public function test_create_role(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/roles', ['name' => 'moderator'])
            ->assertRedirect();

        $this->assertDatabaseHas('roles', ['name' => 'moderator']);
    }

    public function test_create_role_validates_unique(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/roles', ['name' => 'admin'])
            ->assertSessionHasErrors('name');
    }

    public function test_delete_role(): void
    {
        Role::firstOrCreate(['name' => 'temp-role']);

        $role = Role::where('name', 'temp-role')->first();

        $this->actingAs($this->admin())
            ->delete("/admin/roles/{$role->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('roles', ['name' => 'temp-role']);
    }

    public function test_cannot_delete_admin_role(): void
    {
        $role = Role::where('name', 'admin')->first();

        $this->actingAs($this->admin())
            ->delete("/admin/roles/{$role->id}")
            ->assertStatus(403);
    }

    public function test_permissions_matrix_page_shows_roles_and_permissions(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/roles')
            ->assertSee('View Users')
            ->assertSee('Edit Settings');
    }

    public function test_sync_permissions_saves_matrix(): void
    {
        $role = Role::where('name', 'editor')->first();
        $perm = Permission::where('name', 'edit users')->first();

        $this->actingAs($this->admin())
            ->put('/admin/roles/permissions', [
                'matrix' => [
                    $role->id => [$perm->id],
                ],
            ])
            ->assertRedirect();

        $this->assertTrue($role->fresh()->hasPermissionTo('edit users'));
    }

    public function test_sync_permissions_removes_unchecked(): void
    {
        $role = Role::where('name', 'editor')->first();
        $role->givePermissionTo('view users');

        $this->actingAs($this->admin())
            ->put('/admin/roles/permissions', [
                'matrix' => [], // nothing checked for editor
            ])
            ->assertRedirect();

        $this->assertFalse($role->fresh()->hasPermissionTo('view users'));
    }

    // --- Permission groups ---

    public function test_matrix_shows_permission_groups(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/roles')
            ->assertStatus(200)
            ->assertSee('users')     // group label
            ->assertSee('settings'); // group label
    }

    // --- Guard middleware per permission ---

    public function test_manager_with_permission_can_access_users(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager'); // manager has view users

        $this->actingAs($manager)
            ->get('/admin/users')
            ->assertStatus(200);
    }

    public function test_editor_without_create_permission_cannot_create_user(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor'); // editor has only view users

        $this->actingAs($editor)
            ->get('/admin/users/create')
            ->assertStatus(403);
    }

    public function test_editor_without_edit_permission_cannot_edit_user(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');
        $target = User::factory()->create();
        $target->assignRole('user');

        $this->actingAs($editor)
            ->get("/admin/users/{$target->id}/edit")
            ->assertStatus(403);
    }

    public function test_editor_without_delete_permission_cannot_delete_user(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');
        $target = User::factory()->create();
        $target->assignRole('user');

        $this->actingAs($editor)
            ->delete("/admin/users/{$target->id}")
            ->assertStatus(403);
    }

    public function test_editor_without_settings_permission_cannot_access_settings(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $this->actingAs($editor)
            ->get('/admin/settings')
            ->assertStatus(403);
    }

    public function test_manager_with_settings_permission_can_access_settings(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $manager->givePermissionTo('view settings');

        $this->actingAs($manager)
            ->get('/admin/settings')
            ->assertStatus(200);
    }

    // --- Rename role ---

    public function test_admin_can_rename_role(): void
    {
        $role = Role::create(['name' => 'tester', 'guard_name' => 'web']);

        $this->actingAs($this->admin())
            ->put("/admin/roles/{$role->id}", ['name' => 'qa-engineer'])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertEquals('qa-engineer', $role->fresh()->name);
    }

    public function test_rename_prevents_duplicate_name(): void
    {
        $role = Role::create(['name' => 'tester', 'guard_name' => 'web']);

        $this->actingAs($this->admin())
            ->put("/admin/roles/{$role->id}", ['name' => 'editor'])
            ->assertSessionHasErrors('name');
    }

    public function test_cannot_rename_admin_role(): void
    {
        $admin = Role::where('name', 'admin')->first();

        $this->actingAs($this->admin())
            ->put("/admin/roles/{$admin->id}", ['name' => 'superuser'])
            ->assertStatus(403);
    }
}
