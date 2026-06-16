<?php

namespace Tests\Feature\Install;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallGuardTest extends TestCase
{
    use RefreshDatabase;

    private string $lockFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lockFile = storage_path('install.lock');
        @unlink($this->lockFile);
    }

    protected function tearDown(): void
    {
        @unlink($this->lockFile);
        parent::tearDown();
    }

    public function test_install_index_redirects_to_requirements_without_lock(): void
    {
        $this->get('/install')->assertRedirect('/install/requirements');
    }

    public function test_requirements_step_accessible_without_lock(): void
    {
        $this->get('/install/requirements')->assertOk();
    }

    public function test_database_step_accessible_without_lock(): void
    {
        $this->get('/install/database')->assertOk();
    }

    public function test_admin_step_accessible_without_lock(): void
    {
        $this->get('/install/admin')->assertOk();
    }

    public function test_license_step_accessible_without_lock(): void
    {
        $this->get('/install/license')->assertOk();
    }

    public function test_finish_step_accessible_without_lock(): void
    {
        $this->get('/install/finish')->assertOk();
    }

    public function test_install_routes_blocked_with_lock_file(): void
    {
        file_put_contents($this->lockFile, now()->toDateTimeString());

        $this->get('/install')->assertNotFound();
        $this->get('/install/requirements')->assertNotFound();
        $this->get('/install/database')->assertNotFound();
        $this->post('/install/requirements')->assertNotFound();
        $this->post('/install/database')->assertNotFound();
    }

    public function test_invalid_step_returns_404(): void
    {
        $this->get('/install/nonexistent')->assertNotFound();
    }
}
