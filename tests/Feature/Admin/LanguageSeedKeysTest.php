<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use App\Models\LanguageLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageSeedKeysTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_new_language_gets_all_keys_from_lang_files(): void
    {
        $expectedCount = count(\App\Services\LangKeyExtractor::keys('en'));

        $this->actingAs($this->admin())
            ->post('/admin/languages', ['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬'])
            ->assertRedirect();

        $bg = Language::where('code', 'bg')->first();

        $this->assertCount($expectedCount, $bg->lines);
        // Spot-check known keys from lang/en/auth.php
        $this->assertDatabaseHas('language_lines', ['language_id' => $bg->id, 'key' => 'auth.login',  'value' => '']);
        $this->assertDatabaseHas('language_lines', ['language_id' => $bg->id, 'key' => 'auth.logout', 'value' => '']);
        $this->assertDatabaseHas('language_lines', ['language_id' => $bg->id, 'key' => 'nav.dashboard', 'value' => '']);
    }

    public function test_new_language_does_not_duplicate_keys(): void
    {
        $expectedCount = count(\App\Services\LangKeyExtractor::keys('en'));

        $this->actingAs($this->admin())
            ->post('/admin/languages', ['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬'])
            ->assertRedirect();

        // Create same language again — should not duplicate
        $bg = Language::where('code', 'bg')->first();
        // Manually trigger seed again to confirm idempotency
        foreach (\App\Services\LangKeyExtractor::keys('en') as $key) {
            $bg->lines()->firstOrCreate(['key' => $key], ['value' => '']);
        }

        $this->assertSame($expectedCount, LanguageLine::where('language_id', $bg->id)->count());
    }

    public function test_adding_new_line_to_existing_language_seeds_it_to_all_other_languages(): void
    {
        $en = Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true]);
        $bg = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->put("/admin/languages/{$en->id}/lines", ['key' => 'auth.login', 'value' => 'Login'])
            ->assertRedirect();

        // BG should get the key with empty value automatically
        $this->assertDatabaseHas('language_lines', ['language_id' => $bg->id, 'key' => 'auth.login', 'value' => '']);
    }
}
