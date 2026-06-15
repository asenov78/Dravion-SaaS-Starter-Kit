<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create(['email' => 'admin@test.com']);
        $u->assignRole('admin');
        return $u;
    }

    public function test_settings_page_shows_new_fields(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/settings')
            ->assertStatus(200)
            ->assertSee('Timezone')
            ->assertSee('Maintenance')
            ->assertSee('Date Format');
    }

    public function test_saves_timezone(): void
    {
        $this->actingAs($this->admin())
            ->put('/admin/settings', [
                'app_name' => 'Test',
                'app_url'  => 'https://example.com',
                'timezone' => 'Europe/Sofia',
            ])
            ->assertRedirect();

        $this->assertSame('Europe/Sofia', Setting::get('timezone'));
    }

    public function test_saves_date_format(): void
    {
        $this->actingAs($this->admin())
            ->put('/admin/settings', [
                'app_name'    => 'Test',
                'app_url'     => 'https://example.com',
                'date_format' => 'd.m.Y',
            ])
            ->assertRedirect();

        $this->assertSame('d.m.Y', Setting::get('date_format'));
    }

    public function test_saves_maintenance_mode(): void
    {
        $this->actingAs($this->admin())
            ->put('/admin/settings', [
                'app_name'    => 'Test',
                'app_url'     => 'https://example.com',
                'maintenance' => '1',
            ])
            ->assertRedirect();

        $this->assertSame('1', Setting::get('maintenance'));
    }

    public function test_maintenance_mode_blocks_non_admin(): void
    {
        Setting::set('maintenance', '1');

        $regular = User::factory()->create();
        $regular->assignRole('user');

        $this->actingAs($regular)
            ->get('/dashboard')
            ->assertStatus(503);
    }

    public function test_maintenance_mode_allows_admin(): void
    {
        Setting::set('maintenance', '1');

        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertStatus(200);
    }

    // --- Logo upload ---

    public function test_logo_upload_stores_file(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('logo.png', 200, 60);

        $this->actingAs($this->admin())
            ->put('/admin/settings', [
                'app_name' => 'Test',
                'app_url'  => 'https://example.com',
                'logo'     => $file,
            ])
            ->assertRedirect();

        $path = Setting::get('logo');
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_logo_upload_validates_image(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $this->actingAs($this->admin())
            ->put('/admin/settings', [
                'app_name' => 'Test',
                'app_url'  => 'https://example.com',
                'logo'     => $file,
            ])
            ->assertSessionHasErrors('logo');
    }

    public function test_logo_shown_on_settings_page(): void
    {
        Setting::set('logo', 'logos/test.png');

        $this->actingAs($this->admin())
            ->get('/admin/settings')
            ->assertSee('logos/test.png');
    }

    // --- SMTP test ---

    public function test_smtp_test_sends_mail_to_admin(): void
    {
        Mail::fake();

        $this->actingAs($this->admin())
            ->post('/admin/settings/smtp-test')
            ->assertOk()
            ->assertJson(['ok' => true]);

        Mail::assertSent(\App\Mail\SmtpTestMail::class, fn ($m) => $m->hasTo('admin@test.com'));
    }

    public function test_smtp_test_returns_error_on_failure(): void
    {
        Mail::shouldReceive('to->send')->andThrow(new \Exception('Connection refused'));

        $this->actingAs($this->admin())
            ->post('/admin/settings/smtp-test')
            ->assertOk()
            ->assertJson(['ok' => false]);
    }
}
