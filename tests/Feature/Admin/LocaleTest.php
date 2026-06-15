<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_locale_middleware_applies_session_locale(): void
    {
        Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->withSession(['locale' => 'bg'])
            ->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertStatus(200);

        $this->assertSame('bg', app()->getLocale());
    }

    public function test_user_locale_preference_saved(): void
    {
        Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch('/profile/locale', ['locale' => 'bg'])
            ->assertRedirect();

        $this->assertSame('bg', $admin->fresh()->locale);
    }

    public function test_trash_tab_strings_translated_to_bg(): void
    {
        Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $deleted = User::factory()->create(['name' => 'Изтрит Потребител']);
        $deleted->assignRole('user');
        $deleted->delete();

        $response = $this->withSession(['locale' => 'bg'])
            ->actingAs($this->admin())
            ->get('/admin/users?trashed=1')
            ->assertStatus(200);

        $content = $response->getContent();
        $this->assertStringContainsString('Изтрити',          $content); // tab label
        $this->assertStringContainsString('Всички потребители', $content); // all users tab
        $this->assertStringContainsString('Изтрит на',        $content); // column header
        $this->assertStringContainsString('Възстанови',        $content); // restore button
        $this->assertStringContainsString('Всички роли',       $content); // role filter
        $this->assertStringContainsString('Всички статуси',    $content); // status filter
    }

    public function test_export_language_json(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);
        $lang->lines()->create(['key' => 'auth.login', 'value' => 'Вход']);

        $response = $this->actingAs($this->admin())
            ->get("/admin/languages/{$lang->id}/export");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');

        $data = json_decode($response->getContent(), true);
        $this->assertSame('Вход', $data['auth.login']);
    }

    public function test_import_language_json(): void
    {
        $lang = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->post("/admin/languages/{$lang->id}/import", [
                'json' => json_encode(['auth.login' => 'Вход', 'auth.logout' => 'Изход']),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('language_lines', ['language_id' => $lang->id, 'key' => 'auth.login', 'value' => 'Вход']);
        $this->assertDatabaseHas('language_lines', ['language_id' => $lang->id, 'key' => 'auth.logout', 'value' => 'Изход']);
    }
}
