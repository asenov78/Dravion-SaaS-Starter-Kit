<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageManagerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_languages_page_loads(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/languages')
            ->assertStatus(200)
            ->assertSee('Languages');
    }

    public function test_can_add_language(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/languages', ['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬'])
            ->assertRedirect();

        $this->assertDatabaseHas('languages', ['code' => 'bg', 'name' => 'Bulgarian']);
    }

    public function test_cannot_add_duplicate_language(): void
    {
        Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true]);

        $this->actingAs($this->admin())
            ->post('/admin/languages', ['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧'])
            ->assertSessionHasErrors('code');
    }

    public function test_can_set_default_language(): void
    {
        Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true]);
        $bg = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->patch("/admin/languages/{$bg->id}/default")
            ->assertRedirect();

        $this->assertTrue((bool) Language::where('code', 'bg')->value('is_default'));
        $this->assertFalse((bool) Language::where('code', 'en')->value('is_default'));
    }

    public function test_cannot_delete_default_language(): void
    {
        $en = Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true]);

        $this->actingAs($this->admin())
            ->delete("/admin/languages/{$en->id}")
            ->assertStatus(403);
    }

    public function test_can_delete_non_default_language(): void
    {
        Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true]);
        $bg = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->delete("/admin/languages/{$bg->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('languages', ['code' => 'bg']);
    }

    public function test_can_update_translation_line(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->put("/admin/languages/{$lang->id}/lines", [
                'key'   => 'auth.login',
                'value' => 'Вход',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('language_lines', [
            'language_id' => $lang->id,
            'key'         => 'auth.login',
            'value'       => 'Вход',
        ]);
    }

    public function test_locale_switches_via_session(): void
    {
        Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->get('/locale/bg')->assertRedirect();

        $this->assertSame('bg', session('locale'));
    }
}
