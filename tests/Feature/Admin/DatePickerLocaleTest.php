<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies that the admin layout outputs correct window.appLocale and
 * window.appFirstDayOfWeek so flatpickr can use them at runtime.
 */
class DatePickerLocaleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    // ------------------------------------------------------------------ //
    // window.appLocale                                                     //
    // ------------------------------------------------------------------ //

    public function test_layout_outputs_english_locale_by_default(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString("window.appLocale = 'en'", $html);
    }

    public function test_layout_outputs_bg_locale_when_session_locale_is_bg(): void
    {
        $html = $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString("window.appLocale = 'bg'", $html);
    }

    public function test_layout_outputs_bg_locale_when_default_language_setting_is_bg(): void
    {
        Setting::set('default_language', 'bg');
        Setting::flushCache();

        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString("window.appLocale = 'bg'", $html);
    }

    public function test_default_language_setting_bg_overrides_user_profile_locale_en(): void
    {
        // User profile locale 'en' must NOT override the site default_language setting
        // This is the production bug: admin user has locale='en' in DB, setting is 'bg'
        Setting::set('default_language', 'bg');
        Setting::flushCache();

        $user = User::factory()->create(['locale' => 'en']);
        $user->assignRole('admin');

        $html = $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString("window.appLocale = 'bg'", $html);
    }

    // ------------------------------------------------------------------ //
    // window.appFirstDayOfWeek                                            //
    // ------------------------------------------------------------------ //

    public function test_layout_outputs_monday_as_first_day_by_default(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('window.appFirstDayOfWeek = 1', $html);
    }

    public function test_layout_outputs_sunday_when_week_start_setting_is_0(): void
    {
        Setting::set('week_start', '0');
        Setting::flushCache();

        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('window.appFirstDayOfWeek = 0', $html);
    }

    public function test_layout_outputs_monday_when_week_start_setting_is_1(): void
    {
        Setting::set('week_start', '1');
        Setting::flushCache();

        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('window.appFirstDayOfWeek = 1', $html);
    }

    // ------------------------------------------------------------------ //
    // Activity log — flatpickr inputs present (not native type="date")    //
    // ------------------------------------------------------------------ //

    public function test_activity_page_has_no_native_date_inputs(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('admin.activity'))
            ->assertOk()
            ->getContent();

        // Native date inputs use browser OS locale — must not exist
        $this->assertStringNotContainsString('type="date"', $html);
    }

    public function test_activity_page_has_flatpickr_inputs(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('admin.activity'))
            ->assertOk()
            ->getContent();

        // flatpickr inputs are type="text" with name date_from / date_to
        $this->assertStringContainsString('name="date_from"', $html);
        $this->assertStringContainsString('name="date_to"', $html);
        $this->assertStringContainsString('flatpickr', $html);
    }
}
