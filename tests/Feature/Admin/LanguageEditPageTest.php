<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use App\Models\LanguageLine;
use App\Models\User;
use App\Services\LangKeyExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageEditPageTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_edit_page_loads(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);
        $lang->lines()->create(['key' => 'auth.login', 'value' => '']);

        $this->actingAs($this->admin())
            ->get("/admin/languages/{$lang->id}/edit")
            ->assertStatus(200)
            ->assertSee('Bulgarian')
            ->assertSee('auth.login');
    }

    public function test_edit_page_paginates(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);
        for ($i = 0; $i < 60; $i++) {
            $lang->lines()->create(['key' => "group.key_{$i}", 'value' => '']);
        }

        $this->actingAs($this->admin())
            ->get("/admin/languages/{$lang->id}/edit")
            ->assertStatus(200)
            ->assertSee('group.key_0');
    }

    public function test_can_save_batch_translations(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);
        $lang->lines()->createMany([
            ['key' => 'auth.login',  'value' => ''],
            ['key' => 'auth.logout', 'value' => ''],
        ]);

        $this->actingAs($this->admin())
            ->put("/admin/languages/{$lang->id}/batch", [
                'lines' => [
                    'auth.login'  => 'Вход',
                    'auth.logout' => 'Изход',
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('language_lines', ['language_id' => $lang->id, 'key' => 'auth.login',  'value' => 'Вход']);
        $this->assertDatabaseHas('language_lines', ['language_id' => $lang->id, 'key' => 'auth.logout', 'value' => 'Изход']);
    }

    public function test_reseed_en_fills_actual_values(): void
    {
        $en = Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true]);

        $this->actingAs($this->admin())
            ->post("/admin/languages/{$en->id}/reseed")
            ->assertRedirect();

        // EN reseed should use actual values from lang/en/ files
        $this->assertDatabaseHas('language_lines', [
            'language_id' => $en->id,
            'key'         => 'auth.login',
            'value'       => 'Sign In',
        ]);
        $this->assertDatabaseHas('language_lines', [
            'language_id' => $en->id,
            'key'         => 'nav.dashboard',
            'value'       => 'Dashboard',
        ]);
    }
}
