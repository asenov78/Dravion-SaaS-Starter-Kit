<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserEditTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['name' => 'Admin', 'email' => 'admin@dravion.test']);
        $admin->assignRole('admin');
        return $admin;
    }

    public function test_edit_page_loads(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['name' => 'Target User', 'email' => 'target@dravion.test']);
        $target->assignRole('user');

        $this->actingAs($admin)
            ->get("/admin/users/{$target->id}/edit")
            ->assertStatus(200)
            ->assertSee('Target User')
            ->assertSee('target@dravion.test');
    }

    public function test_update_saves_all_profile_fields(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $target->assignRole('user');

        $this->actingAs($admin)
            ->put("/admin/users/{$target->id}", [
                'name'        => 'Updated Name',
                'email'       => 'updated@dravion.test',
                'role'        => 'user',
                'bio'         => 'Developer',
                'phone'       => '+359 888 000 111',
                'country'     => 'Bulgaria',
                'city_state'  => 'Sofia',
                'postal_code' => '1000',
                'tax_id'      => 'BG123456789',
                'facebook'    => 'https://facebook.com/test',
                'x_url'       => 'https://x.com/test',
                'linkedin'    => 'https://linkedin.com/in/test',
                'instagram'   => 'https://instagram.com/test',
            ])
            ->assertRedirect();

        $target->refresh();
        $this->assertSame('Updated Name', $target->name);
        $this->assertSame('updated@dravion.test', $target->email);
        $this->assertSame('Developer', $target->bio);
        $this->assertSame('+359 888 000 111', $target->phone);
        $this->assertSame('Bulgaria', $target->country);
        $this->assertSame('Sofia', $target->city_state);
        $this->assertSame('1000', $target->postal_code);
        $this->assertSame('BG123456789', $target->tax_id);
        $this->assertSame('https://facebook.com/test', $target->facebook);
        $this->assertSame('https://x.com/test', $target->x_url);
        $this->assertSame('https://linkedin.com/in/test', $target->linkedin);
        $this->assertSame('https://instagram.com/test', $target->instagram);
    }

    public function test_update_validates_required_fields(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $target->assignRole('user');

        $this->actingAs($admin)
            ->put("/admin/users/{$target->id}", ['name' => '', 'email' => 'bad', 'role' => 'user'])
            ->assertSessionHasErrors('name');
        // note: 'email' errors also exist but we check just 'name' since 'role' is valid
    }

    public function test_success_alert_shown_after_update(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['name' => 'Old Name']);
        $target->assignRole('user');

        $response = $this->actingAs($admin)
            ->put("/admin/users/{$target->id}", [
                'name'  => 'New Name',
                'email' => $target->email,
                'role'  => 'user',
            ]);

        $response->assertSessionHas('success');

        $this->actingAs($admin)
            ->followingRedirects()
            ->put("/admin/users/{$target->id}", [
                'name'  => 'New Name',
                'email' => $target->email,
                'role'  => 'user',
            ])
            ->assertSee('User updated');
    }

    public function test_password_update_optional(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['password' => bcrypt('original')]);
        $target->assignRole('user');
        $oldHash = $target->password;

        $this->actingAs($admin)
            ->put("/admin/users/{$target->id}", [
                'name'     => $target->name,
                'email'    => $target->email,
                'role'     => 'user',
                'password' => '',
            ])
            ->assertRedirect();

        $target->refresh();
        $this->assertSame($oldHash, $target->password);
    }
}
