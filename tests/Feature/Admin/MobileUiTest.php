<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileUiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create(['email_verified_at' => now()]);
        $u->assignRole('admin');
        return $u;
    }

    private function html(string $route): string
    {
        return $this->actingAs($this->admin())->get($route)->assertOk()->getContent();
    }

    // ── Dark mode: no alpha backgrounds ──────────────────────────────────

    public function test_dashboard_has_no_alpha_dark_backgrounds(): void
    {
        $html = $this->html('/admin/dashboard');
        $this->assertStringNotContainsString('dark:bg-white/[0.03]', $html);
        $this->assertStringNotContainsString('dark:hover:bg-white/[0.02]', $html);
    }

    public function test_users_index_has_no_alpha_dark_backgrounds(): void
    {
        $html = $this->html('/admin/users');
        $this->assertStringNotContainsString('dark:bg-white/[0.03]', $html);
        $this->assertStringNotContainsString('dark:hover:bg-white/[0.03]', $html);
    }

    public function test_settings_has_no_alpha_dark_backgrounds(): void
    {
        $html = $this->html('/admin/settings');
        $this->assertStringNotContainsString('dark:bg-white/[0.03]', $html);
    }

    public function test_roles_page_has_no_alpha_dark_backgrounds(): void
    {
        $html = $this->html('/admin/roles');
        $this->assertStringNotContainsString('dark:bg-white/[0.03]', $html);
        $this->assertStringNotContainsString('dark:hover:bg-white/[0.02]', $html);
    }

    public function test_activity_page_has_no_alpha_dark_backgrounds(): void
    {
        $html = $this->html('/admin/activity');
        $this->assertStringNotContainsString('dark:hover:bg-white/[0.02]', $html);
    }

    // ── Mobile layout ─────────────────────────────────────────────────────

    public function test_notification_dropdown_has_responsive_width(): void
    {
        $html = $this->html('/admin/dashboard');
        // Must not have a fixed 350px width without viewport constraint
        $this->assertStringNotContainsString('width:350px', $html);
    }

    public function test_bulk_action_bar_has_flex_wrap(): void
    {
        $html = $this->html('/admin/users');
        // The action button container must allow wrapping on mobile
        $this->assertStringContainsString('flex-wrap', $html);
    }

    public function test_roles_create_input_allows_full_width(): void
    {
        $html = $this->html('/admin/roles');
        // max-w-xs on input would push button off-screen on mobile
        $this->assertStringNotContainsString('max-w-xs', $html);
    }

    // ── i18n: no hardcoded English in production views ────────────────────

    public function test_dashboard_health_labels_use_translations(): void
    {
        $html = $this->html('/admin/dashboard');
        // Health labels must come from __() — checking for raw English hardcoded in PHP array
        $this->assertStringNotContainsString("'label' => 'Memory Limit'", $html);
        $this->assertStringNotContainsString("'label' => 'Cache Driver'", $html);
    }

    public function test_sidebar_badges_use_translations(): void
    {
        $html = $this->html('/admin/dashboard');
        // "new" and "UPDATE" badge text must not be hardcoded
        $this->assertStringNotContainsString('>new<', $html);
        $this->assertStringNotContainsString('>UPDATE<', $html);
    }

    public function test_header_license_text_uses_translations(): void
    {
        $html = $this->html('/admin/dashboard');
        $this->assertStringNotContainsString("'Dev License'", $html);
        $this->assertStringNotContainsString("'Unlicensed'", $html);
    }

    public function test_updates_page_key_prefix_uses_translation(): void
    {
        // Ensure $masked is truthy so the key_prefix line renders in the view
        config(['dravion.license_key' => 'DEV-TESTKEY1234567890ABCDEF']);
        // Use BG locale so translated key_prefix is 'Ключ:' not 'Key:'
        // If hardcoded, 'Key: ' would still appear; if translated, only 'Ключ:' appears
        app()->setLocale('bg');
        $html = $this->html('/admin/updates');
        $this->assertStringNotContainsString('>Key: <', $html);
        $this->assertStringContainsString('Ключ:', $html);
    }

    public function test_users_edit_avatar_hint_uses_translation(): void
    {
        app()->setLocale('bg');
        $user = User::factory()->create();
        $html = $this->actingAs($this->admin())
            ->get("/admin/users/{$user->id}/edit")
            ->assertOk()
            ->getContent();
        // BG: 'JPG, PNG, GIF — макс. 2 МБ' — proves __() is used, not hardcoded EN string
        $this->assertStringNotContainsString('JPG, PNG, GIF — max 2 MB', $html);
        $this->assertStringContainsString('макс. 2 МБ', $html);
    }
}
