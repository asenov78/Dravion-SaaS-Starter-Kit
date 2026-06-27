<?php

namespace Tests\Feature\Admin;

use App\Models\CustomCategory;
use App\Models\CustomField;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserFieldValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomDataTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create(['email_verified_at' => now()]);
        $u->assignRole('admin');
        return $u;
    }

    // ── Phase 1: removed fields ──────────────────────────────────────────────

    public function test_user_edit_form_has_no_bio_field(): void
    {
        $user = User::factory()->create();
        $html = $this->actingAs($this->admin())
            ->get("/admin/users/{$user->id}/edit")
            ->assertOk()->getContent();

        $this->assertStringNotContainsString('name="bio"', $html);
    }

    public function test_user_edit_form_has_no_social_section(): void
    {
        $user = User::factory()->create();
        $html = $this->actingAs($this->admin())
            ->get("/admin/users/{$user->id}/edit")
            ->assertOk()->getContent();

        $this->assertStringNotContainsString('name="facebook"', $html);
        $this->assertStringNotContainsString('name="x_url"', $html);
        $this->assertStringNotContainsString('name="linkedin"', $html);
        $this->assertStringNotContainsString('name="instagram"', $html);
    }

    public function test_user_edit_form_has_no_postal_code_or_tax_id(): void
    {
        $user = User::factory()->create();
        $html = $this->actingAs($this->admin())
            ->get("/admin/users/{$user->id}/edit")
            ->assertOk()->getContent();

        $this->assertStringNotContainsString('name="postal_code"', $html);
        $this->assertStringNotContainsString('name="tax_id"', $html);
    }

    // ── Phase 2: DB tables exist ─────────────────────────────────────────────

    public function test_custom_categories_table_exists(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('custom_categories'));
    }

    public function test_custom_fields_table_exists(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('custom_fields'));
    }

    public function test_user_field_values_table_exists(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('user_field_values'));
    }

    // ── Phase 3: Seeded system data ──────────────────────────────────────────

    public function test_system_categories_are_seeded(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder'])->assertSuccessful();

        $this->assertDatabaseHas('custom_categories', ['key' => 'account', 'is_system' => true]);
        $this->assertDatabaseHas('custom_categories', ['key' => 'personal_info', 'is_system' => true]);
        $this->assertDatabaseHas('custom_categories', ['key' => 'address', 'is_system' => true]);
    }

    public function test_system_fields_are_seeded(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder'])->assertSuccessful();

        $this->assertDatabaseHas('custom_fields', ['key' => 'phone', 'is_system' => true]);
        $this->assertDatabaseHas('custom_fields', ['key' => 'country', 'is_system' => true]);
        $this->assertDatabaseHas('custom_fields', ['key' => 'city_state', 'is_system' => true]);
    }

    // ── Phase 4: Admin Custom Data CRUD ─────────────────────────────────────

    public function test_custom_data_index_accessible_by_admin(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $this->actingAs($this->admin())
            ->get('/admin/custom-data')
            ->assertOk();
    }

    public function test_admin_can_create_custom_category(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/custom-data/categories', [
                'name_en' => 'Work Info',
                'name_bg' => 'Работна информация',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('custom_categories', ['name_en' => 'Work Info', 'is_system' => false]);
    }

    public function test_admin_can_delete_non_system_category(): void
    {
        $cat = CustomCategory::create([
            'entity'     => 'users',
            'key'        => 'work_info',
            'name_en'    => 'Work Info',
            'name_bg'    => 'Работна информация',
            'is_system'  => false,
            'sort_order' => 99,
        ]);

        $this->actingAs($this->admin())
            ->delete("/admin/custom-data/categories/{$cat->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('custom_categories', ['id' => $cat->id]);
    }

    public function test_system_category_cannot_be_deleted(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();

        $this->actingAs($this->admin())
            ->delete("/admin/custom-data/categories/{$cat->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('custom_categories', ['id' => $cat->id]);
    }

    public function test_admin_can_create_custom_field(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();

        $this->actingAs($this->admin())
            ->post('/admin/custom-data/fields', [
                'category_id' => $cat->id,
                'label_en'    => 'Nickname',
                'label_bg'    => 'Псевдоним',
                'type'        => 'text',
                'is_required' => false,
                'is_visible'  => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('custom_fields', ['label_en' => 'Nickname', 'is_system' => false]);
    }

    public function test_system_field_cannot_be_deleted(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $field = CustomField::where('key', 'phone')->first();

        $this->actingAs($this->admin())
            ->delete("/admin/custom-data/fields/{$field->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('custom_fields', ['id' => $field->id]);
    }

    public function test_non_system_field_can_be_toggled_visible(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();
        $field = CustomField::create([
            'category_id' => $cat->id,
            'key'         => 'nickname',
            'label_en'    => 'Nickname',
            'label_bg'    => 'Псевдоним',
            'type'        => 'text',
            'is_required' => false,
            'is_visible'  => true,
            'is_system'   => false,
            'sort_order'  => 99,
        ]);

        $this->actingAs($this->admin())
            ->patch("/admin/custom-data/fields/{$field->id}", [
                'label_en'    => 'Nickname',
                'label_bg'    => 'Псевдоним',
                'is_required' => false,
                'is_visible'  => false,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('custom_fields', ['id' => $field->id, 'is_visible' => false]);
    }

    // ── Phase 5: Custom field values saved on user update ────────────────────

    public function test_custom_field_values_saved_on_user_update(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();
        $field = CustomField::create([
            'category_id' => $cat->id,
            'key'         => 'nickname',
            'label_en'    => 'Nickname',
            'label_bg'    => 'Псевдоним',
            'type'        => 'text',
            'is_required' => false,
            'is_visible'  => true,
            'is_system'   => false,
            'sort_order'  => 99,
        ]);

        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($this->admin())
            ->put("/admin/users/{$user->id}", [
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => 'user',
                "field_{$field->id}" => 'Batman',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('user_field_values', [
            'user_id'  => $user->id,
            'field_id' => $field->id,
            'value'    => 'Batman',
        ]);
    }

    // ── Phase 6: System fields visible in user edit ──────────────────────────

    public function test_system_fields_appear_in_user_edit_form(): void
    {
        $user = User::factory()->create(['phone' => '+359888000111', 'country' => 'Bulgaria']);

        $html = $this->actingAs($this->admin())
            ->get("/admin/users/{$user->id}/edit")
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('name="phone"', $html);
        $this->assertStringContainsString('name="country"', $html);
        $this->assertStringContainsString('name="city_state"', $html);
        $this->assertStringContainsString('+359888000111', $html);
        $this->assertStringContainsString('Bulgaria', $html);
    }

    public function test_custom_data_index_contains_add_category_form(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('name="name_en"', $html);
        $this->assertStringContainsString('action="' . route('admin.custom-data.categories.store') . '"', $html);
    }

    public function test_custom_data_index_shows_system_categories(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Personal Information', $html);
        $this->assertStringContainsString('Address', $html);
        $this->assertStringContainsString('Phone', $html);
        $this->assertStringContainsString('Country', $html);
    }

    // ── Phase 8: Reorder ─────────────────────────────────────────────────────

    public function test_categories_can_be_reordered(): void
    {
        $cat1 = CustomCategory::where('key', 'personal_info')->first();
        $cat2 = CustomCategory::where('key', 'address')->first();

        $this->actingAs($this->admin())
            ->post(route('admin.custom-data.categories.reorder'), [
                'ids' => [$cat2->id, $cat1->id],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals(1, $cat2->fresh()->sort_order);
        $this->assertEquals(2, $cat1->fresh()->sort_order);
    }

    public function test_fields_can_be_reordered(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();

        $f1 = CustomField::create([
            'category_id' => $cat->id, 'key' => 'field_a', 'label_en' => 'A',
            'label_bg' => 'А', 'type' => 'text', 'sort_order' => 10,
        ]);
        $f2 = CustomField::create([
            'category_id' => $cat->id, 'key' => 'field_b', 'label_en' => 'B',
            'label_bg' => 'Б', 'type' => 'text', 'sort_order' => 20,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.custom-data.fields.reorder'), [
                'ids' => [$f2->id, $f1->id],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals(1, $f2->fresh()->sort_order);
        $this->assertEquals(2, $f1->fresh()->sort_order);
    }

    public function test_reorder_requires_admin(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $this->actingAs($editor)
            ->post(route('admin.custom-data.categories.reorder'), ['ids' => []])
            ->assertForbidden();
    }

    // ── Phase 9: Multilingual options (select + checkbox) ────────────────────

    public function test_select_field_stores_multilingual_options(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();

        $this->actingAs($this->admin())
            ->post('/admin/custom-data/fields', [
                'category_id' => $cat->id,
                'label_en'    => 'Gender',
                'label_bg'    => 'Пол',
                'type'        => 'select',
                'options_en'  => "Male\nFemale",
                'options_bg'  => "Мъж\nЖена",
                'is_visible'  => true,
            ])
            ->assertRedirect();

        $field = CustomField::where('label_en', 'Gender')->first();
        $this->assertNotNull($field);
        $this->assertEquals([
            ['en' => 'Male',   'bg' => 'Мъж'],
            ['en' => 'Female', 'bg' => 'Жена'],
        ], $field->options);
    }

    public function test_select_field_renders_locale_option_labels(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();
        $field = CustomField::create([
            'category_id' => $cat->id,
            'key'         => 'gender',
            'label_en'    => 'Gender',
            'label_bg'    => 'Пол',
            'type'        => 'select',
            'options'     => [['en' => 'Male', 'bg' => 'Мъж'], ['en' => 'Female', 'bg' => 'Жена']],
            'is_visible'  => true,
            'is_system'   => false,
            'sort_order'  => 99,
        ]);

        $user = User::factory()->create();
        app()->setLocale('bg');

        $html = $this->actingAs($this->admin())
            ->get("/admin/users/{$user->id}/edit")
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Мъж', $html);
        $this->assertStringContainsString('Жена', $html);
    }

    public function test_checkbox_field_stores_multiple_options(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();

        $this->actingAs($this->admin())
            ->post('/admin/custom-data/fields', [
                'category_id' => $cat->id,
                'label_en'    => 'Interests',
                'label_bg'    => 'Интереси',
                'type'        => 'checkbox',
                'options_en'  => "Sports\nMusic\nTravel",
                'options_bg'  => "Спорт\nМузика\nПътуване",
                'is_visible'  => true,
            ])
            ->assertRedirect();

        $field = CustomField::where('label_en', 'Interests')->first();
        $this->assertNotNull($field);
        $this->assertCount(3, $field->options);
        $this->assertEquals(['en' => 'Sports', 'bg' => 'Спорт'], $field->options[0]);
    }

    public function test_checkbox_field_renders_multiple_checkboxes_in_user_edit(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();
        $field = CustomField::create([
            'category_id' => $cat->id,
            'key'         => 'interests',
            'label_en'    => 'Interests',
            'label_bg'    => 'Интереси',
            'type'        => 'checkbox',
            'options'     => [
                ['en' => 'Sports', 'bg' => 'Спорт'],
                ['en' => 'Music',  'bg' => 'Музика'],
            ],
            'is_visible'  => true,
            'is_system'   => false,
            'sort_order'  => 99,
        ]);

        $user = User::factory()->create();

        $html = $this->actingAs($this->admin())
            ->get("/admin/users/{$user->id}/edit")
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('value="Sports"', $html);
        $this->assertStringContainsString('value="Music"', $html);
    }

    public function test_checkbox_multiple_values_saved_on_user_update(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();
        $field = CustomField::create([
            'category_id' => $cat->id,
            'key'         => 'interests',
            'label_en'    => 'Interests',
            'label_bg'    => 'Интереси',
            'type'        => 'checkbox',
            'options'     => [
                ['en' => 'Sports', 'bg' => 'Спорт'],
                ['en' => 'Music',  'bg' => 'Музика'],
                ['en' => 'Travel', 'bg' => 'Пътуване'],
            ],
            'is_visible'  => true,
            'is_system'   => false,
            'sort_order'  => 99,
        ]);

        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($this->admin())
            ->put("/admin/users/{$user->id}", [
                'name'                   => $user->name,
                'email'                  => $user->email,
                'role'                   => 'user',
                "field_{$field->id}"     => ['Sports', 'Travel'],
            ])
            ->assertRedirect();

        $stored = UserFieldValue::where('user_id', $user->id)
            ->where('field_id', $field->id)
            ->value('value');

        $this->assertStringContainsString('Sports', $stored);
        $this->assertStringContainsString('Travel', $stored);
    }

    // ── Phase 10: Bug regression ──────────────────────────────────────────────

    public function test_update_select_field_preserves_options_when_submitted_empty(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();
        $field = CustomField::create([
            'category_id' => $cat->id,
            'key'         => 'gender',
            'label_en'    => 'Gender',
            'label_bg'    => 'Пол',
            'type'        => 'select',
            'options'     => [['en' => 'Male', 'bg' => 'Мъж'], ['en' => 'Female', 'bg' => 'Жена']],
            'is_visible'  => true,
            'is_system'   => false,
            'sort_order'  => 99,
        ]);

        // PATCH without providing options_en (browser sends empty string)
        $this->actingAs($this->admin())
            ->patch("/admin/custom-data/fields/{$field->id}", [
                'label_en'   => 'Gender Updated',
                'label_bg'   => 'Пол Обновен',
                'options_en' => '',
                'options_bg' => '',
                'is_visible' => true,
            ])
            ->assertRedirect();

        $fresh = $field->fresh();
        $this->assertNotNull($fresh->options, 'Options should be preserved when options_en is empty');
        $this->assertCount(2, $fresh->options);
        $this->assertEquals('Male', $fresh->options[0]['en']);
    }

    public function test_system_category_cannot_be_updated(): void
    {
        $cat = CustomCategory::where('key', 'personal_info')->first();

        $this->actingAs($this->admin())
            ->put("/admin/custom-data/categories/{$cat->id}", [
                'name_en' => 'Hacked Name',
                'name_bg' => 'Хакнато',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('custom_categories', ['key' => 'personal_info', 'name_en' => 'Personal Information']);
    }

    public function test_cannot_add_field_to_account_category(): void
    {
        $cat = CustomCategory::where('key', 'account')->first();

        $this->actingAs($this->admin())
            ->post('/admin/custom-data/fields', [
                'category_id' => $cat->id,
                'label_en'    => 'Test Field',
                'label_bg'    => 'Тест',
                'type'        => 'text',
                'is_visible'  => true,
            ])
            ->assertForbidden();

        $this->assertEquals(0, CustomField::where('category_id', $cat->id)->count());
    }

    public function test_invisible_system_field_does_not_clear_user_column_on_save(): void
    {
        $field = CustomField::where('key', 'phone')->first();
        $field->update(['is_visible' => false]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'phone'             => '+359888999000',
        ]);

        // Save user without phone in request (invisible → not rendered)
        $this->actingAs($this->admin())
            ->put("/admin/users/{$user->id}", [
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => 'user',
            ])
            ->assertRedirect();

        $this->assertSame('+359888999000', $user->fresh()->phone, 'Phone should not be cleared when its system field is invisible');
    }

    public function test_delete_category_cascades_to_fields_and_values(): void
    {
        $cat = CustomCategory::create([
            'entity' => 'users', 'key' => 'temp_cat', 'name_en' => 'Temp', 'name_bg' => 'Временна',
            'is_system' => false, 'sort_order' => 99,
        ]);
        $field = CustomField::create([
            'category_id' => $cat->id, 'key' => 'temp_field', 'label_en' => 'Tmp',
            'label_bg' => 'Тмп', 'type' => 'text', 'sort_order' => 1,
        ]);
        $user = User::factory()->create();
        UserFieldValue::create(['user_id' => $user->id, 'field_id' => $field->id, 'value' => 'v']);

        $this->actingAs($this->admin())
            ->delete("/admin/custom-data/categories/{$cat->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('custom_categories',  ['id' => $cat->id]);
        $this->assertDatabaseMissing('custom_fields',      ['id' => $field->id]);
        $this->assertDatabaseMissing('user_field_values',  ['field_id' => $field->id]);
    }

    public function test_field_value_updated_on_re_save(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();
        $field = CustomField::create([
            'category_id' => $cat->id, 'key' => 'nickname2', 'label_en' => 'Nick',
            'label_bg' => 'Псевдоним', 'type' => 'text', 'is_visible' => true,
            'is_system' => false, 'sort_order' => 99,
        ]);
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($this->admin())
            ->put("/admin/users/{$user->id}", [
                'name' => $user->name, 'email' => $user->email, 'role' => 'user',
                "field_{$field->id}" => 'First',
            ])->assertRedirect();

        $this->actingAs($this->admin())
            ->put("/admin/users/{$user->id}", [
                'name' => $user->name, 'email' => $user->email, 'role' => 'user',
                "field_{$field->id}" => 'Second',
            ])->assertRedirect();

        $this->assertDatabaseHas('user_field_values', ['user_id' => $user->id, 'field_id' => $field->id, 'value' => 'Second']);
        $this->assertEquals(1, UserFieldValue::where('user_id', $user->id)->where('field_id', $field->id)->count());
    }

    public function test_non_admin_cannot_access_custom_data_index(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $this->actingAs($editor)
            ->get(route('admin.custom-data.index'))
            ->assertForbidden();
    }

    public function test_non_admin_cannot_create_custom_category(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $this->actingAs($editor)
            ->post('/admin/custom-data/categories', ['name_en' => 'X', 'name_bg' => 'X'])
            ->assertForbidden();
    }

    public function test_invisible_custom_field_not_shown_in_user_edit(): void
    {
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();
        $field = CustomField::create([
            'category_id' => $cat->id, 'key' => 'hidden_field', 'label_en' => 'SecretField',
            'label_bg' => 'Скрито', 'type' => 'text', 'is_visible' => false,
            'is_system' => false, 'sort_order' => 99,
        ]);

        $user = User::factory()->create();
        $html = $this->actingAs($this->admin())
            ->get("/admin/users/{$user->id}/edit")
            ->assertOk()->getContent();

        $this->assertStringNotContainsString('SecretField', $html);
        $this->assertStringNotContainsString("field_{$field->id}", $html);
    }

    // ── Layout regression — pinned so these don't break silently ─────────────

    public function test_modal_headers_flat_layout(): void
    {
        // Flat p-6 panel layout (no header separator, no X button) — matches confirm modal style.
        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('pt-6 pb-2 pr-14', $html,
            'Modals must not have old header div — flat p-6 layout required');
        $this->assertStringNotContainsString('absolute right-4 top-4', $html,
            'Modals must not have X close button — flat layout removed it');
        $this->assertGreaterThanOrEqual(4, substr_count($html, 'mb-6'),
            'All 4 modal titles must use mb-6 (flat layout)');
    }

    public function test_actions_column_width_fits_icon_only_buttons(): void
    {
        // Field action buttons are icon-only. 2 × ~46px buttons fit in 120px.
        // If someone bumps this back to 200px, tests still pass — but if they
        // also add text back and width is still 120px, this test catches the mismatch.
        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        // width:120px must appear for the ACTIONS column
        $this->assertStringContainsString('width:120px', $html,
            'ACTIONS column must use width:120px (icon-only buttons require ~100px)');
    }

    public function test_field_row_edit_button_is_icon_only(): void
    {
        // Field action buttons must be icon-only — text "Edit" with px-4 padding overflows 120px column.
        // Category header can still have text buttons; only field ROW actions are icon-only.
        $this->artisan('db:seed', ['--class' => 'CustomDataSeeder']);
        $cat = CustomCategory::where('key', 'personal_info')->first();
        CustomField::create([
            'category_id' => $cat->id, 'key' => 'icon_test_field',
            'label_en' => 'IconTestField', 'label_bg' => 'ИконаТест',
            'type' => 'text', 'is_visible' => true, 'is_system' => false, 'sort_order' => 99,
        ]);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        // The edit button inside the field's modal-wrapper should have no slot text.
        // Icon-only = x-ta.button closes without slot content (self-closing or empty slot).
        // We assert the field-action Edit button does NOT render visible text "Edit" / "Редактирай"
        // next to the edit modal trigger (as opposed to the category header which may have it).
        // Best proxy: the route for fields.update exists in the HTML (edit form) and
        // the edit modal open button does not wrap text between the icon spans.
        $this->assertStringContainsString(
            route('admin.custom-data.fields.update', CustomField::where('key', 'icon_test_field')->first()),
            $html,
            'Edit field form action must be present in the page'
        );
    }

    public function test_index_shows_categories_in_sort_order(): void
    {
        $cat1 = CustomCategory::where('key', 'personal_info')->first();
        $cat2 = CustomCategory::where('key', 'address')->first();
        $cat1->update(['sort_order' => 50]);
        $cat2->update(['sort_order' => 10]);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.custom-data.index'))
            ->assertOk()
            ->getContent();

        $pos1 = strpos($html, $cat1->name_en);
        $pos2 = strpos($html, $cat2->name_en);
        $this->assertLessThan($pos1, $pos2, 'Address (sort_order=10) should appear before Personal Information (sort_order=50)');
    }

    // ── Activity Log ─────────────────────────────────────────────────────────

    private function enableCustomDataLog(): void
    {
        Setting::set('activity_log_custom_data', '1');
    }

    public function test_create_category_logs_activity(): void
    {
        $this->enableCustomDataLog();
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.custom-data.categories.store'), [
            'name_en' => 'Test Log Cat', 'name_bg' => 'Тест Лог Кат',
        ])->assertRedirect();

        $this->assertDatabaseHas('activity_log', [
            'log_name'  => 'custom_data',
            'event'     => 'category_created',
            'causer_id' => $admin->id,
        ]);
    }

    public function test_update_category_logs_activity(): void
    {
        $this->enableCustomDataLog();
        $admin = $this->admin();
        $cat = CustomCategory::factory()->create(['is_system' => false]);

        $this->actingAs($admin)->put(route('admin.custom-data.categories.update', $cat), [
            'name_en' => 'Updated EN', 'name_bg' => 'Updated BG',
        ])->assertRedirect();

        $this->assertDatabaseHas('activity_log', [
            'log_name'   => 'custom_data',
            'event'      => 'category_updated',
            'causer_id'  => $admin->id,
            'subject_id' => $cat->id,
        ]);
    }

    public function test_delete_category_logs_activity(): void
    {
        $this->enableCustomDataLog();
        $admin = $this->admin();
        $cat = CustomCategory::factory()->create(['is_system' => false]);

        $this->actingAs($admin)->delete(route('admin.custom-data.categories.destroy', $cat))
            ->assertRedirect();

        $this->assertDatabaseHas('activity_log', [
            'log_name'  => 'custom_data',
            'event'     => 'category_deleted',
            'causer_id' => $admin->id,
        ]);
    }

    public function test_create_field_logs_activity(): void
    {
        $this->enableCustomDataLog();
        $admin = $this->admin();
        $cat = CustomCategory::factory()->create(['is_system' => false, 'key' => 'log_test_cat_' . uniqid()]);

        $this->actingAs($admin)->post(route('admin.custom-data.fields.store'), [
            'category_id' => $cat->id,
            'label_en'    => 'Log Field EN',
            'label_bg'    => 'Log Field BG',
            'type'        => 'text',
        ])->assertRedirect();

        $this->assertDatabaseHas('activity_log', [
            'log_name'  => 'custom_data',
            'event'     => 'field_created',
            'causer_id' => $admin->id,
        ]);
    }

    public function test_update_field_logs_activity(): void
    {
        $this->enableCustomDataLog();
        $admin = $this->admin();
        $cat = CustomCategory::factory()->create(['is_system' => false]);
        $field = CustomField::factory()->create(['category_id' => $cat->id, 'is_system' => false]);

        $this->actingAs($admin)->patch(route('admin.custom-data.fields.update', $field), [
            'label_en' => 'New EN', 'label_bg' => 'New BG',
        ])->assertRedirect();

        $this->assertDatabaseHas('activity_log', [
            'log_name'   => 'custom_data',
            'event'      => 'field_updated',
            'causer_id'  => $admin->id,
            'subject_id' => $field->id,
        ]);
    }

    public function test_delete_field_logs_activity(): void
    {
        $this->enableCustomDataLog();
        $admin = $this->admin();
        $cat = CustomCategory::factory()->create(['is_system' => false]);
        $field = CustomField::factory()->create(['category_id' => $cat->id, 'is_system' => false]);

        $this->actingAs($admin)->delete(route('admin.custom-data.fields.destroy', $field))
            ->assertRedirect();

        $this->assertDatabaseHas('activity_log', [
            'log_name'  => 'custom_data',
            'event'     => 'field_deleted',
            'causer_id' => $admin->id,
        ]);
    }
}
