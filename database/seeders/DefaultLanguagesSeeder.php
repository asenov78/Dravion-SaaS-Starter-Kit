<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultLanguagesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('languages')->insertOrIgnore([
            ['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true,  'created_at' => now(), 'updated_at' => now()],
            ['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
