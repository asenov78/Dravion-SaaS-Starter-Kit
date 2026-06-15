<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_welcome_email_sent_when_admin_creates_user(): void
    {
        Mail::fake();

        $this->actingAs($this->admin())
            ->post('/admin/users', [
                'name'     => 'New User',
                'email'    => 'newuser@example.com',
                'password' => 'password123',
                'role'     => 'user',
            ]);

        Mail::assertSent(\App\Mail\WelcomeMail::class, fn ($m) => $m->hasTo('newuser@example.com'));
    }

    public function test_welcome_email_not_sent_when_setting_disabled(): void
    {
        Mail::fake();
        \App\Models\Setting::set('mail_welcome', '0');

        $this->actingAs($this->admin())
            ->post('/admin/users', [
                'name'     => 'Silent User',
                'email'    => 'silent@example.com',
                'password' => 'password123',
                'role'     => 'user',
            ]);

        Mail::assertNotSent(\App\Mail\WelcomeMail::class);
    }

    public function test_suspend_notification_sent_to_user(): void
    {
        Notification::fake();

        $target = User::factory()->create();
        $target->assignRole('user');

        $this->actingAs($this->admin())
            ->patch("/admin/users/{$target->id}/suspend");

        Notification::assertSentTo($target, \App\Notifications\AccountSuspendedNotification::class);
    }

    public function test_activate_notification_sent_to_user(): void
    {
        Notification::fake();

        $target = User::factory()->create(['status' => 'suspended']);
        $target->assignRole('user');

        $this->actingAs($this->admin())
            ->patch("/admin/users/{$target->id}/activate");

        Notification::assertSentTo($target, \App\Notifications\AccountActivatedNotification::class);
    }
}
