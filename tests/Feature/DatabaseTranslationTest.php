<?php

namespace Tests\Feature;

use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear translator group cache between tests so DB changes are picked up
        app('translator')->setLoaded([]);
    }

    public function test_translates_from_db_when_locale_set(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);
        $lang->lines()->create(['key' => 'nav.dashboard', 'value' => 'Табло']);

        app()->setLocale('bg');

        $this->assertSame('Табло', __('nav.dashboard'));
    }

    public function test_falls_back_to_locale_file_when_db_value_empty(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);
        $lang->lines()->create(['key' => 'nav.dashboard', 'value' => '']);

        app()->setLocale('bg');

        // BG file has 'Табло'; DB value is empty so file wins over EN fallback
        $this->assertSame('Табло', __('nav.dashboard'));
    }

    public function test_falls_back_to_file_when_language_not_in_db(): void
    {
        app()->setLocale('en');

        $this->assertSame('Dashboard', __('nav.dashboard'));
    }

    public function test_nested_keys_work(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);
        $lang->lines()->create(['key' => 'auth.failed', 'value' => 'Грешни данни.']);

        app()->setLocale('bg');

        $this->assertSame('Грешни данни.', __('auth.failed'));
    }
}
