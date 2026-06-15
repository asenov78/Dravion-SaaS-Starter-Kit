<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiShowcaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_showcase_pages(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $pages = [
            'ecommerce', 'form-elements', 'tables', 'alerts', 'avatars', 'badges',
            'buttons', 'images', 'videos', 'profile', 'calendar', 'bar-chart',
            'line-chart', 'blank',
        ];

        foreach ($pages as $page) {
            $this->actingAs($admin)
                ->get('/admin/ui/'.$page)
                ->assertStatus(200);
        }
    }
}
