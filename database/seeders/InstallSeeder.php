<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * InstallSeeder — runs on every fresh install via the installer wizard.
 *
 * RULE: every feature that ships default data MUST add a seeder here.
 * New seeder = new class in this directory + one $this->call() line below.
 * The installer (InstallController) never needs to be touched for data changes.
 *
 * Order matters: roles before settings before pages (foreign keys / config deps).
 */
class InstallSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            DefaultLanguagesSeeder::class,
            DefaultSettingsSeeder::class,
            DefaultPagesSeeder::class,
        ]);
    }
}
