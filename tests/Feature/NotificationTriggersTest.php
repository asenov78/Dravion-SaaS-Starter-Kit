<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AccountActivatedNotification;
use App\Notifications\AccountSuspendedNotification;
use App\Notifications\NewUserRegisteredNotification;
use App\Notifications\UpdateInstalledNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTriggersTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create(['email_verified_at' => now()]);
        $u->assignRole('admin');
        return $u;
    }

    private function regularUser(): User
    {
        $u = User::factory()->create(['email_verified_at' => now()]);
        $u->assignRole('user');
        return $u;
    }

    // --- Suspend / Activate store in DB ---

    public function test_suspend_sends_database_notification_to_user(): void
    {
        Notification::fake();
        $admin  = $this->admin();
        $target = $this->regularUser();

        $this->actingAs($admin)->patch("/admin/users/{$target->id}/suspend");

        Notification::assertSentTo($target, AccountSuspendedNotification::class, function ($n) {
            return in_array('database', $n->via(new User));
        });
    }

    public function test_activate_sends_database_notification_to_user(): void
    {
        Notification::fake();
        $admin  = $this->admin();
        $target = $this->regularUser();
        $target->update(['status' => 'suspended']);

        $this->actingAs($admin)->patch("/admin/users/{$target->id}/activate");

        Notification::assertSentTo($target, AccountActivatedNotification::class, function ($n) {
            return in_array('database', $n->via(new User));
        });
    }

    // --- New user registration notifies admins ---

    public function test_registration_notifies_admins(): void
    {
        Notification::fake();
        $admin = $this->admin();

        $this->post('/register', [
            'name'                  => 'New Guy',
            'email'                 => 'newguy@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        Notification::assertSentTo($admin, NewUserRegisteredNotification::class);
    }

    public function test_admin_create_user_notifies_admins(): void
    {
        Notification::fake();
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/users', [
            'name'     => 'Another User',
            'email'    => 'another@example.com',
            'password' => 'password123',
            'role'     => 'user',
        ]);

        Notification::assertSentTo($admin, NewUserRegisteredNotification::class);
    }

    // --- Update installed notifies admins ---

    public function test_update_install_notifies_admins(): void
    {
        Notification::fake();
        $admin = $this->admin();

        $mock = $this->mock(\App\Services\UpdaterService::class);
        $mock->shouldReceive('downloadAndInstall')
            ->once()
            ->andReturn(['ok' => true, 'version' => '1.4.0']);

        // LicenseService::isValid() reads config — fake a valid license key
        config(['dravion.license_key' => 'DEV-TEST']);

        $this->actingAs($admin)
            ->post('/admin/updates/install', ['zip_url' => 'https://api.github.com/repos/asenov78/Dravion-SaaS-Starter-Kit/zipball/v1.4.0'])
            ->assertOk();

        Notification::assertSentTo($admin, UpdateInstalledNotification::class);
    }

    // --- toArray contains expected keys ---

    public function test_suspended_notification_has_correct_data(): void
    {
        $user  = $this->regularUser();
        $notif = new AccountSuspendedNotification();
        $data  = $notif->toArray($user);

        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('body', $data);
        $this->assertArrayHasKey('url', $data);
    }

    public function test_activated_notification_has_correct_data(): void
    {
        $user  = $this->regularUser();
        $notif = new AccountActivatedNotification();
        $data  = $notif->toArray($user);

        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('body', $data);
        $this->assertArrayHasKey('url', $data);
    }

    public function test_new_user_notification_has_correct_data(): void
    {
        $newUser = $this->regularUser();
        $admin   = $this->admin();
        $notif   = new NewUserRegisteredNotification($newUser);
        $data    = $notif->toArray($admin);

        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('body', $data);
        $this->assertArrayHasKey('url', $data);
    }
}
