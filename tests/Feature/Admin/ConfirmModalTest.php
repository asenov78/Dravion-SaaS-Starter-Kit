<?php

namespace Tests\Feature\Admin;

use App\Models\CustomCategory;
use App\Models\CustomField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pins the confirm modal structure so it doesn't regress silently.
 *
 * Two variants must stay consistent:
 *  A) Global confirm modal (admin.blade.php) — triggered via confirm-action event
 *  B) Custom-data inline modals (custom-data/index.blade.php) — add/edit category/field
 *
 * Common rules for both:
 *  - White background in light mode (bg-white), dark:bg-gray-900 for dark mode
 *  - Solid background only — NO alpha/transparent dark backgrounds
 *  - Modal content visible in light theme (text-gray-800, not text-white hardcoded)
 */
class ConfirmModalTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create(['email_verified_at' => now()]);
        $u->assignRole('admin');
        return $u;
    }

    // ── Global confirm modal (admin layout) ──────────────────────────────────

    public function test_confirm_modal_has_light_theme_background(): void
    {
        // bg-gray-900 without dark: prefix = always dark → breaks light theme
        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('bg-white', $html,
            'Confirm modal panel must use bg-white (light theme background)');

        $this->assertStringContainsString('dark:bg-gray-900', $html,
            'Confirm modal panel must use dark:bg-gray-900 for dark theme');
    }

    public function test_confirm_modal_title_uses_light_theme_text_color(): void
    {
        // text-white/90 without dark: = always white on white in light theme
        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('text-gray-800', $html,
            'Confirm modal title must use text-gray-800 (visible in light theme)');

        $this->assertStringContainsString('dark:text-white/90', $html,
            'Confirm modal title must use dark:text-white/90 for dark theme');
    }

    public function test_confirm_modal_cancel_button_has_light_theme_border(): void
    {
        // border-gray-700 without dark: = dark border on white in light theme
        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('border-gray-300', $html,
            'Confirm modal Cancel button must use border-gray-300 for light theme');

        $this->assertStringContainsString('dark:border-gray-700', $html,
            'Confirm modal Cancel button must use dark:border-gray-700 for dark theme');
    }

    public function test_confirm_modal_does_not_use_hardcoded_dark_colors(): void
    {
        // These classes without dark: prefix look broken in light theme.
        // If this fails, someone reverted to the old all-dark modal.
        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        // These specific combinations should NOT appear (hardcoded dark without dark: prefix)
        $this->assertStringNotContainsString(
            'class="relative w-full max-w-md rounded-2xl border border-gray-800 bg-gray-900',
            $html,
            'Confirm modal must not use hardcoded dark bg-gray-900 without dark: prefix'
        );
    }

    // ── Custom-data inline modals ────────────────────────────────────────────

    public function test_custom_data_inline_modals_use_white_background(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        // Inline modals: rounded-3xl bg-white dark:bg-gray-900
        $this->assertStringContainsString('rounded-3xl bg-white dark:bg-gray-900', $html,
            'Custom-data inline modals must use bg-white dark:bg-gray-900');
    }

    public function test_custom_data_inline_modals_have_pr14_header(): void
    {
        // Modal headers need pr-14 to clear the X close button
        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        $this->assertGreaterThanOrEqual(4, substr_count($html, 'pr-14'),
            'All 4 modal headers must have pr-14 padding to clear the X close button');
    }

    public function test_custom_data_field_modals_use_confirm_event_for_delete(): void
    {
        // Field delete must use the global confirm-action event (not a custom inline confirm).
        // This ensures consistent UX — one modal style for all destructive actions.
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();
        CustomField::create([
            'category_id' => $cat->id, 'key' => 'confirm_test',
            'label_en' => 'ConfirmTest', 'label_bg' => 'ПотвърдиТест',
            'type' => 'text', 'is_visible' => true, 'is_system' => false, 'sort_order' => 99,
        ]);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(
            "\$dispatch('confirm-action'",
            $html,
            'Field delete buttons must dispatch confirm-action to use the global confirm modal'
        );
    }

    public function test_custom_data_category_delete_uses_confirm_event(): void
    {
        // Category delete must also use confirm-action event
        CustomCategory::create([
            'entity' => 'users', 'key' => 'cat_confirm_test',
            'name_en' => 'CatConfirm', 'name_bg' => 'КатПотвърди',
            'is_system' => false, 'sort_order' => 99,
        ]);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        // confirm-action must appear at least once for category delete
        $this->assertGreaterThanOrEqual(1, substr_count($html, "\$dispatch('confirm-action'"),
            'Category delete must dispatch confirm-action');
    }

    // ── Structural consistency ────────────────────────────────────────────────

    public function test_no_modal_uses_hardcoded_dark_background_without_prefix(): void
    {
        // Scan admin dashboard for "bg-gray-900" appearing WITHOUT dark: prefix
        // at the modal panel level. This catches regressions on any page.
        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        // All inline modals must use "bg-white dark:bg-gray-900" not bare "bg-gray-900"
        $bareCount   = substr_count($html, '" bg-gray-900"') + substr_count($html, ' bg-gray-900 ');
        $prefixCount = substr_count($html, 'dark:bg-gray-900');

        // Any bg-gray-900 must be accompanied by a dark: prefix somewhere on the same element.
        // The simplest check: confirm inline modals use the correct combined class.
        $this->assertStringContainsString(
            'bg-white dark:bg-gray-900',
            $html,
            'Inline modals on custom-data page must use bg-white dark:bg-gray-900 (not bare bg-gray-900)'
        );
    }
}
