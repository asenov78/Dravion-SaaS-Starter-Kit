<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'app_name'              => config('app.name', 'Dravion'),
            'app_url'               => config('app.url', ''),
            'mail_from'             => 'noreply@example.com',
            'mail_from_name'        => config('app.name', 'Dravion'),
            'mail_welcome'          => '0',
            'registration'          => '1',
            'timezone'              => 'UTC',
            'date_format'           => 'd/m/Y',
            'maintenance'           => '0',
            'logo'                  => '',
            'default_language'      => 'en',
            'activity_log_auth'        => '1',
            'activity_log_users'       => '1',
            'activity_log_profile'     => '1',
            'activity_log_settings'    => '1',
            'activity_log_custom_data' => '1',
            'broadcast_banner'      => '',
            'footer_text'           => '',
            'footer_copyright'      => '',
            'header_tagline'        => '',
        ];

        foreach ($defaults as $key => $value) {
            // insertOrIgnore — never overwrites existing values (safe for updates too)
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
