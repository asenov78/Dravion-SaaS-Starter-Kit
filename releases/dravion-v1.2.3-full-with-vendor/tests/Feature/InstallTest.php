<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class InstallTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure no lock file during tests
        if (file_exists(storage_path('install.lock'))) {
            unlink(storage_path('install.lock'));
        }
    }

    protected function tearDown(): void
    {
        if (file_exists(storage_path('install.lock'))) {
            unlink(storage_path('install.lock'));
        }
        parent::tearDown();
    }

    // ── InstallGuard ──────────────────────────────────────────────────────

    public function test_installer_accessible_without_lock(): void
    {
        $this->get('/install')->assertRedirect('/install/requirements');
    }

    public function test_installer_blocked_after_lock_exists(): void
    {
        file_put_contents(storage_path('install.lock'), 'installed');
        $this->get('/install')->assertNotFound();
    }

    // ── Requirements ─────────────────────────────────────────────────────

    public function test_requirements_page_loads(): void
    {
        $this->get('/install/requirements')->assertOk()->assertSee('Requirements', false);
    }

    public function test_requirements_post_redirects_to_database(): void
    {
        $this->post('/install/requirements')->assertRedirect('/install/database');
    }

    // ── Database ─────────────────────────────────────────────────────────

    public function test_database_page_loads(): void
    {
        $this->get('/install/database')->assertOk()->assertSee('Database', false);
    }

    public function test_database_validates_required_fields(): void
    {
        $this->post('/install/database', [])->assertSessionHasErrors(['db_host', 'db_name', 'db_user']);
    }

    public function test_database_invalid_connection_returns_error(): void
    {
        $this->post('/install/database', [
            'db_host'     => '127.0.0.1',
            'db_port'     => '3306',
            'db_name'     => 'nonexistent_db_xyz',
            'db_user'     => 'bad_user',
            'db_password' => 'bad_pass',
        ])->assertSessionHasErrors(['db_host']);
    }

    // ── Admin ─────────────────────────────────────────────────────────────

    public function test_admin_page_loads(): void
    {
        $this->get('/install/admin')->assertOk()->assertSee('Admin', false);
    }

    public function test_admin_validates_required_fields(): void
    {
        $this->post('/install/admin', [])->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_admin_validates_password_confirmation(): void
    {
        $this->post('/install/admin', [
            'name'                  => 'Admin',
            'email'                 => 'admin@test.com',
            'password'              => 'secret123',
            'password_confirmation' => 'wrong',
        ])->assertSessionHasErrors(['password']);
    }

    public function test_admin_valid_stores_in_session_and_redirects(): void
    {
        $this->post('/install/admin', [
            'name'                  => 'Admin User',
            'email'                 => 'admin@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/install/license');

        $this->assertEquals('admin@test.com', session('install_admin.email'));
    }

    // ── License ───────────────────────────────────────────────────────────

    public function test_license_page_loads(): void
    {
        $this->get('/install/license')->assertOk()->assertSee('License', false);
    }

    public function test_license_validates_purchase_code(): void
    {
        $this->post('/install/license', [])->assertSessionHasErrors(['purchase_code']);
    }

    public function test_license_valid_stores_in_session_and_redirects(): void
    {
        $this->post('/install/license', ['purchase_code' => 'TEST-1234-ABCD'])
            ->assertRedirect('/install/finish');

        $this->assertEquals('TEST-1234-ABCD', session('install_license.purchase_code'));
    }

    // ── Finish ────────────────────────────────────────────────────────────

    public function test_finish_page_loads(): void
    {
        $this->get('/install/finish')->assertOk()->assertSee('Finish', false);
    }

    public function test_finish_creates_admin_user_and_lock(): void
    {
        session([
            'install_admin'   => ['name' => 'Admin', 'email' => 'admin@ex.com', 'password' => 'password123'],
            'install_license' => ['purchase_code' => 'TEST-CODE'],
        ]);

        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder', '--force' => true]);

        $this->post('/install/finish')->assertRedirect('/login');

        $this->assertTrue(file_exists(storage_path('install.lock')));
        $this->assertDatabaseHas('users', ['email' => 'admin@ex.com']);

        $user = User::where('email', 'admin@ex.com')->first();
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_installer_blocked_after_finish(): void
    {
        file_put_contents(storage_path('install.lock'), now());
        $this->get('/install/requirements')->assertNotFound();
    }
}
