<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    private function seedNotification(User $user, bool $read = false): DatabaseNotification
    {
        $notif = $user->notifications()->create([
            'id'              => Str::uuid(),
            'type'            => 'App\Notifications\TestNotification',
            'data'            => ['title' => 'Test', 'body' => 'Hello world', 'url' => '/dashboard'],
            'read_at'         => $read ? now() : null,
            'notifiable_type' => User::class,
            'notifiable_id'   => $user->id,
        ]);
        return $notif;
    }

    // --- JSON feed ---

    public function test_notifications_feed_returns_json(): void
    {
        $user = $this->admin();
        $this->seedNotification($user);

        $this->actingAs($user)
            ->getJson('/notifications')
            ->assertOk()
            ->assertJsonStructure(['unread_count', 'notifications']);
    }

    public function test_unread_count_correct(): void
    {
        $user = $this->admin();
        $this->seedNotification($user, read: false);
        $this->seedNotification($user, read: false);
        $this->seedNotification($user, read: true);

        $res = $this->actingAs($user)->getJson('/notifications')->assertOk();
        $this->assertEquals(2, $res->json('unread_count'));
    }

    public function test_feed_returns_latest_20(): void
    {
        $user = $this->admin();
        for ($i = 0; $i < 25; $i++) {
            $this->seedNotification($user);
        }

        $res = $this->actingAs($user)->getJson('/notifications')->assertOk();
        $this->assertCount(20, $res->json('notifications'));
    }

    // --- Mark single read ---

    public function test_mark_single_notification_read(): void
    {
        $user = $this->admin();
        $notif = $this->seedNotification($user);

        $this->actingAs($user)
            ->postJson("/notifications/{$notif->id}/read")
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertNotNull($notif->fresh()->read_at);
    }

    public function test_cannot_mark_other_users_notification_read(): void
    {
        $user  = $this->admin();
        $other = User::factory()->create(); $other->assignRole('user');
        $notif = $this->seedNotification($other);

        $this->actingAs($user)
            ->postJson("/notifications/{$notif->id}/read")
            ->assertStatus(403);
    }

    // --- Mark all read ---

    public function test_mark_all_read(): void
    {
        $user = $this->admin();
        $this->seedNotification($user);
        $this->seedNotification($user);

        $this->actingAs($user)
            ->postJson('/notifications/read-all')
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    // --- Auth guard ---

    public function test_unauthenticated_redirected_from_feed(): void
    {
        $this->get('/notifications')->assertRedirect('/login');
    }
}
