<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(CustomDataSeeder::class);

        $admin = User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@dravion.test',
        ]);
        $admin->assignRole('admin');

        $manager = User::factory()->create([
            'name'  => 'Manager User',
            'email' => 'manager@dravion.test',
        ]);
        $manager->assignRole('manager');

        User::factory(8)->create()->each(fn ($u) => $u->assignRole('user'));
    }
}
