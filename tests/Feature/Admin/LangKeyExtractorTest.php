<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use App\Models\User;
use App\Services\LangKeyExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LangKeyExtractorTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_extractor_returns_flat_keys_from_lang_files(): void
    {
        $keys = LangKeyExtractor::keys();

        $this->assertIsArray($keys);
        $this->assertNotEmpty($keys);
        // All keys must be dot-notation strings
        foreach ($keys as $key) {
            $this->assertIsString($key);
            $this->assertStringContainsString('.', $key);
        }
    }

    public function test_new_language_seeded_from_lang_files(): void
    {
        $keyCount = count(LangKeyExtractor::keys());

        $this->actingAs($this->admin())
            ->post('/admin/languages', ['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬'])
            ->assertRedirect();

        $bg = Language::where('code', 'bg')->first();
        $this->assertCount($keyCount, $bg->lines);
    }

    public function test_all_seeded_lines_have_empty_value(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/languages', ['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬'])
            ->assertRedirect();

        $bg = Language::where('code', 'bg')->first();
        $nonEmpty = $bg->lines()->where('value', '!=', '')->count();
        $this->assertSame(0, $nonEmpty);
    }

    public function test_header_shows_language_switcher_with_multiple_languages(): void
    {
        Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true]);
        Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('🇬🇧')
            ->assertSee('🇧🇬');
    }

    public function test_header_hides_switcher_with_single_language(): void
    {
        Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true]);

        $response = $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertStatus(200);

        // Only one language — no switcher dropdown needed
        $response->assertDontSee('locale/bg');
    }
}
