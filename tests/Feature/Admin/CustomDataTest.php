<?php

namespace Tests\Feature\Admin;

use App\Models\CustomCategory;
use App\Models\CustomField;
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

    // ── Phase 6: Reorder ─────────────────────────────────────────────────────

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
}
