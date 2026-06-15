<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageMetaEditTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_meta_edit_page_loads(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->get("/admin/languages/{$lang->id}/meta")
            ->assertStatus(200)
            ->assertSee('Bulgarian')
            ->assertSee('bg');
    }

    public function test_can_update_language_meta(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->patch("/admin/languages/{$lang->id}/meta", [
                'name' => 'Български',
                'flag' => '🇧🇬',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('languages', ['id' => $lang->id, 'name' => 'Български']);
    }

    public function test_cannot_change_code_to_existing_one(): void
    {
        Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true]);
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->patch("/admin/languages/{$lang->id}/meta", [
                'name' => 'Bulgarian',
                'code' => 'en',
                'flag' => '🇧🇬',
            ])
            ->assertSessionHasErrors('code');
    }
}
