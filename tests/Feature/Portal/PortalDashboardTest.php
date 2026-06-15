<?php

namespace Tests\Feature\Portal;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        return $user;
    }

    public function test_guest_can_view_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertStatus(200);
    }

    public function test_auth_user_can_view_dashboard(): void
    {
        $this->actingAs($this->user())
            ->get(route('dashboard'))
            ->assertStatus(200);
    }

    public function test_admin_can_also_view_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertStatus(200);
    }

    public function test_dashboard_shows_system_name(): void
    {
        $this->actingAs($this->user())
            ->get(route('dashboard'))
            ->assertSee(config('app.name'));
    }

    public function test_dashboard_shows_version(): void
    {
        $this->actingAs($this->user())
            ->get(route('dashboard'))
            ->assertSee(config('dravion.version'));
    }

    public function test_dashboard_shows_user_name(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSee($user->name);
    }

    public function test_dashboard_has_logout(): void
    {
        $this->actingAs($this->user())
            ->get(route('dashboard'))
            ->assertSee('logout');
    }
}
