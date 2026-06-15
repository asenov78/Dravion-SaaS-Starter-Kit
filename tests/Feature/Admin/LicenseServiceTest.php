<?php

namespace Tests\Feature\Admin;

use App\Services\LicenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        @unlink(storage_path('license.cache'));
        parent::tearDown();
    }

    public function test_empty_key_is_invalid(): void
    {
        config(['dravion.license_key' => '']);
        $this->assertFalse(LicenseService::isValid());
    }

    public function test_production_key_without_cache_is_valid(): void
    {
        config(['dravion.license_key' => 'DRV-ABC123']);
        @unlink(storage_path('license.cache'));
        $this->assertTrue(LicenseService::isValid());
    }

    public function test_production_key_with_invalid_cache_is_invalid(): void
    {
        config(['dravion.license_key' => 'DRV-ABC123']);
        file_put_contents(storage_path('license.cache'), json_encode(['valid' => false, 'checked_at' => time()]));
        $this->assertFalse(LicenseService::isValid());
    }

    public function test_dev_key_is_valid_on_dev_domain(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'http://localhost']);
        $this->assertTrue(LicenseService::isValid());
    }
}
