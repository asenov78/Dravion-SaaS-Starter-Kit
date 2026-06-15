<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use App\Models\LanguageLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageDeleteReseedTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_cannot_delete_last_language(): void
    {
        $only = Language::create(['code' => 'bgn', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => true]);

        $this->actingAs($this->admin())
            ->delete("/admin/languages/{$only->id}")
            ->assertStatus(403);
    }

    public function test_can_delete_default_language_when_others_exist(): void
    {
        $bg = Language::create(['code' => 'bgn', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => true]);
        $en = Language::create(['code' => 'en',  'name' => 'English',   'flag' => '🇬🇧', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->delete("/admin/languages/{$bg->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('languages', ['code' => 'bgn']);
        // EN must be promoted to default
        $this->assertSame(1, (int) Language::where('code', 'en')->value('is_default'));
    }

    public function test_reseed_fills_missing_keys(): void
    {
        $bg = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => true]);
        // bg has 0 lines

        $this->actingAs($this->admin())
            ->post("/admin/languages/{$bg->id}/reseed")
            ->assertRedirect();

        $expectedCount = count(\App\Services\LangKeyExtractor::keys('en'));
        $this->assertSame($expectedCount, $bg->lines()->count());
    }

    public function test_reseed_does_not_overwrite_existing_translations(): void
    {
        $bg = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => true]);
        $bg->lines()->create(['key' => 'auth.login', 'value' => 'Вход']);

        $this->actingAs($this->admin())
            ->post("/admin/languages/{$bg->id}/reseed")
            ->assertRedirect();

        // Existing translation must be preserved
        $this->assertDatabaseHas('language_lines', ['language_id' => $bg->id, 'key' => 'auth.login', 'value' => 'Вход']);
    }
}
