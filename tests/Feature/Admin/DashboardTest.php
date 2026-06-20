<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Facades\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_dashboard_shows_user_stats(): void
    {
        User::factory()->count(3)->create()->each(fn ($u) => $u->assignRole('user'));

        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('Total Users')
            ->assertSee('Active Users');
    }

    public function test_dashboard_shows_recent_activity(): void
    {
        $admin = $this->admin();
        ActivityLogger::log('auth', 'login', 'Test login event', null, $admin);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('Recent Activity');
    }

    public function test_dashboard_shows_new_users_this_month(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('New This Month');
    }

    public function test_dashboard_shows_system_health(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('System Health')
            ->assertSee('PHP');
    }
}

