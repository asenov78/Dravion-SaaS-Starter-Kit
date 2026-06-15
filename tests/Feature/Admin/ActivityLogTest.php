<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create(['name' => 'Admin', 'email' => 'admin@test.test']);
        $u->assignRole('admin');
        return $u;
    }

    private function enableAll(): void
    {
        Setting::setMany([
            'activity_log_auth'     => '1',
            'activity_log_users'    => '1',
            'activity_log_profile'  => '1',
            'activity_log_settings' => '1',
        ]);
    }

    // --- Auth events ---

    public function test_login_creates_activity_when_enabled(): void
    {
        $this->enableAll();
        $admin = $this->admin();

        $this->post('/login', ['email' => 'admin@test.test', 'password' => 'password']);

        $this->assertDatabaseHas('activity_log', [
            'log_name'  => 'auth',
            'event'     => 'login',
            'causer_id' => $admin->id,
        ]);
    }

    public function test_login_no_activity_when_disabled(): void
    {
        Setting::set('activity_log_auth', '0');
        $this->admin();

        $this->post('/login', ['email' => 'admin@test.test', 'password' => 'password']);

        $this->assertDatabaseMissing('activity_log', ['log_name' => 'auth', 'event' => 'login']);
    }

    public function test_logout_creates_activity_when_enabled(): void
    {
        $this->enableAll();
        $admin = $this->admin();

        $this->actingAs($admin)->post('/logout');

        $this->assertDatabaseHas('activity_log', [
            'log_name'  => 'auth',
            'event'     => 'logout',
            'causer_id' => $admin->id,
        ]);
    }

    // --- User events ---

    public function test_user_created_logs_activity(): void
    {
        $this->enableAll();
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/users', [
            'name'     => 'New User',
            'email'    => 'newuser@test.test',
            'password' => 'password123',
            'role'     => 'user',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name'    => 'users',
            'event'       => 'created',
            'causer_id'   => $admin->id,
        ]);
    }

    public function test_user_updated_logs_activity(): void
    {
        $this->enableAll();
        $admin = $this->admin();
        $target = User::factory()->create();
        $target->assignRole('user');

        $this->actingAs($admin)->put("/admin/users/{$target->id}", [
            'name'  => 'Changed Name',
            'email' => $target->email,
            'role'  => 'user',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name'  => 'users',
            'event'     => 'updated',
            'causer_id' => $admin->id,
        ]);
    }

    public function test_user_suspended_logs_activity(): void
    {
        $this->enableAll();
        $admin = $this->admin();
        $target = User::factory()->create();
        $target->assignRole('user');

        $this->actingAs($admin)->patch("/admin/users/{$target->id}/suspend");

        $this->assertDatabaseHas('activity_log', [
            'log_name'  => 'users',
            'event'     => 'suspended',
            'causer_id' => $admin->id,
        ]);
    }

    public function test_user_events_skipped_when_disabled(): void
    {
        Setting::set('activity_log_users', '0');
        $admin = $this->admin();
        $target = User::factory()->create();
        $target->assignRole('user');

        $this->actingAs($admin)->put("/admin/users/{$target->id}", [
            'name'  => 'X',
            'email' => $target->email,
            'role'  => 'user',
        ]);

        $this->assertDatabaseMissing('activity_log', ['log_name' => 'users']);
    }

    // --- Profile events ---

    public function test_profile_update_logs_activity(): void
    {
        $this->enableAll();
        $admin = $this->admin();

        $this->actingAs($admin)->put('/admin/ui/profile', [
            'name'  => 'Admin New',
            'email' => 'admin@test.test',
            'bio'   => 'Team Lead',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name'  => 'profile',
            'event'     => 'updated',
            'causer_id' => $admin->id,
        ]);
    }

    // --- Settings events ---

    public function test_settings_update_logs_activity(): void
    {
        $this->enableAll();
        $admin = $this->admin();

        $this->actingAs($admin)->put('/admin/settings', [
            'app_name' => 'My App',
            'app_url'  => 'https://example.com',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name'  => 'settings',
            'event'     => 'updated',
            'causer_id' => $admin->id,
        ]);
    }

    // --- Activity log page ---

    public function test_activity_page_shows_entries(): void
    {
        $this->enableAll();
        $admin = $this->admin();

        activity('auth')->causedBy($admin)->event('login')->log('User logged in');

        $this->actingAs($admin)
            ->get('/admin/activity')
            ->assertStatus(200)
            ->assertSee('login');
    }

    // --- Filters ---

    public function test_filter_by_log_name(): void
    {
        $admin = $this->admin();
        activity('auth')->causedBy($admin)->log('login event');
        activity('users')->causedBy($admin)->log('user created');

        $this->actingAs($admin)
            ->get('/admin/activity?log_name=auth')
            ->assertStatus(200)
            ->assertSee('login event')
            ->assertDontSee('user created');
    }

    public function test_filter_by_causer(): void
    {
        $admin = $this->admin();
        $other = User::factory()->create(); $other->assignRole('user');

        activity('auth')->causedBy($admin)->log('admin action');
        activity('auth')->causedBy($other)->log('other action');

        $this->actingAs($admin)
            ->get('/admin/activity?causer_id=' . $other->id)
            ->assertStatus(200)
            ->assertSee('other action')
            ->assertDontSee('admin action');
    }

    public function test_filter_by_date_from(): void
    {
        $admin = $this->admin();
        activity('auth')->causedBy($admin)->log('old entry');

        \Spatie\Activitylog\Models\Activity::latest()->first()
            ->update(['created_at' => now()->subDays(10)]);

        activity('auth')->causedBy($admin)->log('new entry');

        $this->actingAs($admin)
            ->get('/admin/activity?date_from=' . now()->subDays(1)->format('Y-m-d'))
            ->assertStatus(200)
            ->assertSee('new entry')
            ->assertDontSee('old entry');
    }

    // --- Export CSV ---

    public function test_admin_can_export_activity_csv(): void
    {
        $admin = $this->admin();
        activity('auth')->causedBy($admin)->log('exported entry');

        $response = $this->actingAs($admin)
            ->get('/admin/activity/export')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->assertStringContainsString('exported entry', $response->streamedContent());
    }

    public function test_export_respects_filters(): void
    {
        $admin = $this->admin();
        activity('auth')->causedBy($admin)->log('auth event');
        activity('users')->causedBy($admin)->log('users event');

        $response = $this->actingAs($admin)
            ->get('/admin/activity/export?log_name=auth')
            ->assertOk();

        $content = $response->streamedContent();
        $this->assertStringContainsString('auth event', $content);
        $this->assertStringNotContainsString('users event', $content);
    }

    public function test_filter_by_date_to(): void
    {
        $admin = $this->admin();
        activity('auth')->causedBy($admin)->log('recent entry');
        activity('auth')->causedBy($admin)->log('old entry');

        \Spatie\Activitylog\Models\Activity::where('description', 'old entry')
            ->update(['created_at' => now()->subDays(10)]);

        $this->actingAs($admin)
            ->get('/admin/activity?date_to=' . now()->subDays(5)->format('Y-m-d'))
            ->assertStatus(200)
            ->assertSee('old entry')
            ->assertDontSee('recent entry');
    }
}
