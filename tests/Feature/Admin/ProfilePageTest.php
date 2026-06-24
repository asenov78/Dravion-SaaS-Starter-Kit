<?php

namespace Tests\Feature\Admin;

use App\Models\CustomCategory;
use App\Models\CustomField;
use App\Models\User;
use App\Models\UserFieldValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['name' => 'Jane Admin', 'email' => 'jane@dravion.test']);
        $admin->assignRole('admin');
        return $admin;
    }

    public function test_profile_page_shows_current_user(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/ui/profile')
            ->assertStatus(200)
            ->assertSee('Jane Admin')
            ->assertSee('jane@dravion.test');
    }

    public function test_profile_update_persists_fields(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put('/admin/ui/profile', [
                'name'  => 'Jane Updated',
                'email' => 'jane.new@dravion.test',
                'phone' => '+359 888 123 456',
            ])
            ->assertRedirect();

        $admin->refresh();
        $this->assertSame('Jane Updated', $admin->name);
        $this->assertSame('jane.new@dravion.test', $admin->email);
        $this->assertSame('+359 888 123 456', $admin->phone);
    }

    public function test_profile_update_shows_success_alert(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->followingRedirects()
            ->put('/admin/ui/profile', [
                'name'  => 'Jane Admin',
                'email' => 'jane@dravion.test',
                'bio'   => 'CTO',
            ])
            ->assertSee('Profile updated');
    }

    public function test_profile_update_validates_email(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put('/admin/ui/profile', ['name' => 'X', 'email' => 'not-an-email'])
            ->assertSessionHasErrors('email');
    }

    public function test_avatar_is_resized_to_200px(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        // Upload a large 1000x800 image
        $file = UploadedFile::fake()->image('big.jpg', 1000, 800);

        $this->actingAs($admin)->put('/admin/ui/profile', [
            'name' => 'Jane Admin', 'email' => 'jane@dravion.test', 'avatar' => $file,
        ]);

        $admin->refresh();
        $this->assertNotNull($admin->avatar);

        $contents = Storage::disk('public')->get($admin->avatar);
        [$w, $h] = getimagesizefromstring($contents);
        $this->assertLessThanOrEqual(200, $w);
        $this->assertLessThanOrEqual(200, $h);
    }

    public function test_avatar_upload_stores_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $file = UploadedFile::fake()->image('photo.jpg', 200, 200);

        $this->actingAs($admin)
            ->put('/admin/ui/profile', [
                'name'   => 'Jane Admin',
                'email'  => 'jane@dravion.test',
                'avatar' => $file,
            ])
            ->assertRedirect();

        $admin->refresh();
        $this->assertNotNull($admin->avatar);
        Storage::disk('public')->assertExists($admin->avatar);
    }

    public function test_avatar_upload_validates_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $this->actingAs($admin)
            ->put('/admin/ui/profile', [
                'name'   => 'Jane Admin',
                'email'  => 'jane@dravion.test',
                'avatar' => $file,
            ])
            ->assertSessionHasErrors('avatar');
    }

    public function test_profile_page_uses_i18n_strings(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/ui/profile')
            ->assertOk()
            ->assertSee(__('nav.profile'))
            ->assertSee(__('nav.change_password'))
            ->assertDontSee('User Profile');
    }

    public function test_profile_page_uses_bg_strings_in_bg_locale(): void
    {
        app()->setLocale('bg');

        $this->actingAs($this->admin())
            ->get('/admin/ui/profile')
            ->assertOk()
            ->assertSee('Профил')
            ->assertSee('Промяна на парола');
    }

public function test_profile_page_shows_custom_fields(): void
    {
        $admin = $this->admin();

        $cat = CustomCategory::create([
            'entity' => 'users', 'key' => 'profile_test',
            'name_en' => 'Extra Info', 'name_bg' => 'Допълнителна информация',
            'sort_order' => 10,
        ]);
        CustomField::create([
            'category_id' => $cat->id, 'key' => 'linkedin_url', 'type' => 'text',
            'label_en' => 'LinkedIn', 'label_bg' => 'LinkedIn',
            'is_visible' => true, 'is_required' => false, 'is_system' => false, 'sort_order' => 1,
        ]);

        $this->actingAs($admin)
            ->get('/admin/ui/profile')
            ->assertOk()
            ->assertSee('Extra Info')
            ->assertSee('LinkedIn');
    }

    public function test_profile_custom_field_value_saved(): void
    {
        $admin = $this->admin();

        $cat = CustomCategory::create([
            'entity' => 'users', 'key' => 'extra_profile',
            'name_en' => 'Extra', 'name_bg' => 'Допълнителна',
            'sort_order' => 10,
        ]);
        $field = CustomField::create([
            'category_id' => $cat->id, 'key' => 'nickname', 'type' => 'text',
            'label_en' => 'Nickname', 'label_bg' => 'Прякор',
            'is_visible' => true, 'is_required' => false, 'is_system' => false, 'sort_order' => 1,
        ]);

        $this->actingAs($admin)
            ->put('/admin/ui/profile', [
                'name'             => 'Jane Admin',
                'email'            => 'jane@dravion.test',
                "field_{$field->id}" => 'J-Admin',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('user_field_values', [
            'user_id'  => $admin->id,
            'field_id' => $field->id,
            'value'    => 'J-Admin',
        ]);
    }

    public function test_avatar_upload_replaces_old_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $first = UploadedFile::fake()->image('first.jpg');
        $this->actingAs($admin)->put('/admin/ui/profile', [
            'name' => 'Jane Admin', 'email' => 'jane@dravion.test', 'avatar' => $first,
        ]);
        $admin->refresh();
        $oldPath = $admin->avatar;

        $second = UploadedFile::fake()->image('second.jpg');
        $this->actingAs($admin)->put('/admin/ui/profile', [
            'name' => 'Jane Admin', 'email' => 'jane@dravion.test', 'avatar' => $second,
        ]);
        $admin->refresh();

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($admin->avatar);
    }
}
