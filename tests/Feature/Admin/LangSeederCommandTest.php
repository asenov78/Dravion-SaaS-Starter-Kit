<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LangSeederCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_lang_seed_command_creates_en_with_values(): void
    {
        $this->artisan('lang:seed')->assertSuccessful();

        $en = Language::where('code', 'en')->first();
        $this->assertNotNull($en);
        $this->assertDatabaseHas('language_lines', ['language_id' => $en->id, 'key' => 'auth.login',    'value' => 'Sign In']);
        $this->assertDatabaseHas('language_lines', ['language_id' => $en->id, 'key' => 'nav.dashboard', 'value' => 'Dashboard']);
    }

    public function test_lang_seed_command_creates_bg_with_values(): void
    {
        $this->artisan('lang:seed')->assertSuccessful();

        $bg = Language::where('code', 'bg')->first();
        $this->assertNotNull($bg);
        $this->assertDatabaseHas('language_lines', ['language_id' => $bg->id, 'key' => 'auth.login',    'value' => 'Вход']);
        $this->assertDatabaseHas('language_lines', ['language_id' => $bg->id, 'key' => 'nav.dashboard', 'value' => 'Табло']);
    }

    public function test_lang_seed_is_idempotent(): void
    {
        $this->artisan('lang:seed')->assertSuccessful();
        $this->artisan('lang:seed')->assertSuccessful();

        $this->assertSame(2, Language::count());
    }

    public function test_reseed_uses_native_lang_file_when_available(): void
    {
        $this->artisan('lang:seed')->assertSuccessful();

        $bg = Language::where('code', 'bg')->first();

        // Change a value then reseed — should restore from lang/bg/
        $bg->lines()->where('key', 'auth.login')->update(['value' => 'OLD']);

        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post("/admin/languages/{$bg->id}/reseed")
            ->assertRedirect();

        $this->assertDatabaseHas('language_lines', ['language_id' => $bg->id, 'key' => 'auth.login', 'value' => 'Вход']);
    }
}
