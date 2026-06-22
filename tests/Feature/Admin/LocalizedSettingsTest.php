<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizedSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->actingAs($this->admin);
    }

    public function test_settings_page_shows_bg_inputs_for_all_four_localized_fields(): void
    {
        $response = $this->get(route('admin.settings'));
        $response->assertOk();

        // Each field has an _bg variant input
        $response->assertSee('name="broadcast_banner_bg"', false);
        $response->assertSee('name="header_tagline_bg"', false);
        $response->assertSee('name="footer_text_bg"', false);
        $response->assertSee('name="footer_copyright_bg"', false);
    }

    public function test_bg_values_are_prefilled_from_settings(): void
    {
        Setting::set('broadcast_banner_bg', 'Тест банер BG');
        Setting::set('header_tagline_bg', 'Таглайн BG');

        $response = $this->get(route('admin.settings'));
        $response->assertOk();
        $response->assertSee('Тест банер BG');
        $response->assertSee('Таглайн BG');
    }

    public function test_update_saves_bg_variants(): void
    {
        $this->put(route('admin.settings.update'), $this->validPayload([
            'broadcast_banner'    => 'Banner EN',
            'broadcast_banner_bg' => 'Банер BG',
            'header_tagline'      => 'Tagline EN',
            'header_tagline_bg'   => 'Таглайн BG',
            'footer_text'         => 'Footer EN',
            'footer_text_bg'      => 'Футър BG',
            'footer_copyright'    => '© EN',
            'footer_copyright_bg' => '© BG',
        ]))->assertRedirect();

        $this->assertEquals('Банер BG',    Setting::get('broadcast_banner_bg'));
        $this->assertEquals('Таглайн BG',  Setting::get('header_tagline_bg'));
        $this->assertEquals('Футър BG',    Setting::get('footer_text_bg'));
        $this->assertEquals('© BG',        Setting::get('footer_copyright_bg'));

        // EN variants still saved under original keys
        $this->assertEquals('Banner EN',   Setting::get('broadcast_banner'));
        $this->assertEquals('Tagline EN',  Setting::get('header_tagline'));
    }

    public function test_bg_variants_are_optional_empty_saves_empty_string(): void
    {
        Setting::set('broadcast_banner_bg', 'old value');

        $this->put(route('admin.settings.update'), $this->validPayload([
            'broadcast_banner_bg' => '',
        ]))->assertRedirect();

        $this->assertEquals('', Setting::get('broadcast_banner_bg', ''));
    }

    public function test_broadcast_banner_serves_bg_locale_version(): void
    {
        Setting::set('broadcast_banner',    'Banner EN');
        Setting::set('broadcast_banner_bg', 'Банер BG');

        app()->setLocale('bg');
        $this->assertEquals('Банер BG', Setting::getLocalized('broadcast_banner'));

        app()->setLocale('en');
        $this->assertEquals('Banner EN', Setting::getLocalized('broadcast_banner'));
    }

    public function test_getlocalized_falls_back_to_default_key_when_bg_empty(): void
    {
        Setting::set('broadcast_banner',    'Banner EN');
        Setting::set('broadcast_banner_bg', '');

        app()->setLocale('bg');
        $this->assertEquals('Banner EN', Setting::getLocalized('broadcast_banner'));
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'app_name'        => 'Test App',
            'app_url'         => 'https://example.com',
            'default_language'=> 'en',
            'week_start'      => '1',
            'timezone'        => 'UTC',
            'date_format'     => 'd/m/Y',
        ], $overrides);
    }
}
