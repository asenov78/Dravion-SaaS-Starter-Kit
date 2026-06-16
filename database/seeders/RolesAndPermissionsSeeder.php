<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view users', 'create users', 'edit users', 'delete users', 'suspend users',
            'view settings', 'edit settings',
            'view activity log',
            'view pages', 'create pages', 'edit pages', 'delete pages',
            'manage languages',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::firstOrCreate(['name' => 'admin'])->givePermissionTo(Permission::all());

        Role::firstOrCreate(['name' => 'manager'])->givePermissionTo([
            'view users', 'create users', 'edit users', 'suspend users',
            'view activity log',
            'view pages', 'create pages', 'edit pages',
        ]);

        Role::firstOrCreate(['name' => 'editor'])->givePermissionTo([
            'view users',
            'view pages', 'create pages', 'edit pages',
        ]);

        Role::firstOrCreate(['name' => 'user']);
    }
}
