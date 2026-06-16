<?php

namespace Tests\Feature\Admin;

use App\Contracts\LicenseServiceInterface;
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

    private function svc(): LicenseServiceInterface
    {
        return app(LicenseServiceInterface::class);
    }

    public function test_empty_key_is_invalid(): void
    {
        config(['dravion.license_key' => '']);
        $this->assertFalse($this->svc()->isValid());
    }

    public function test_production_key_without_cache_is_invalid(): void
    {
        config(['dravion.license_key' => 'DRV-ABC123']);
        @unlink(storage_path('license.cache'));
        $this->assertFalse($this->svc()->isValid());
    }

    public function test_production_key_with_valid_cache_is_valid(): void
    {
        config(['dravion.license_key' => 'DRV-ABC123']);
        $this->svc()->writeCache(['valid' => true, 'checked_at' => time()]);
        $this->assertTrue($this->svc()->isValid());
    }

    public function test_production_key_with_invalid_cache_is_invalid(): void
    {
        config(['dravion.license_key' => 'DRV-ABC123']);
        $this->svc()->writeCache(['valid' => false, 'checked_at' => time()]);
        $this->assertFalse($this->svc()->isValid());
    }

    public function test_tampered_cache_is_invalid(): void
    {
        config(['dravion.license_key' => 'DRV-ABC123']);
        file_put_contents(storage_path('license.cache'), json_encode(['valid' => true, 'checked_at' => time()]));
        $this->assertFalse($this->svc()->isValid());
    }

    public function test_dev_key_is_valid_on_dev_domain(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'http://localhost']);
        $this->assertTrue($this->svc()->isValid());
    }

    public function test_dev_key_is_invalid_on_production_domain(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'https://mysite.com']);
        $this->assertFalse($this->svc()->isValid());
    }
}
