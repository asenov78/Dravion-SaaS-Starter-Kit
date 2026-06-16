<?php

namespace Tests\Unit;

use App\Services\LicenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LicenseServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_activate_returns_license_key_on_success(): void
    {
        Http::fake([
            '*activate*' => Http::response(['license_key' => 'DRV-TESTKEY123'], 200),
        ]);

        $result = LicenseService::activate('PURCHASE-CODE-123', 'example.com');

        $this->assertArrayHasKey('license_key', $result);
        $this->assertSame('DRV-TESTKEY123', $result['license_key']);
        $this->assertArrayNotHasKey('error', $result);
    }

    public function test_activate_returns_error_on_server_failure(): void
    {
        Http::fake([
            '*activate*' => Http::response(['error' => 'Invalid purchase code'], 422),
        ]);

        $result = LicenseService::activate('BAD-CODE', 'example.com');

        $this->assertArrayHasKey('error', $result);
        $this->assertSame('Invalid purchase code', $result['error']);
    }

    public function test_activate_returns_error_on_network_failure(): void
    {
        Http::fake([
            '*activate*' => fn() => throw new \Illuminate\Http\Client\ConnectionException('timeout'),
        ]);

        $result = LicenseService::activate('CODE', 'example.com');

        $this->assertArrayHasKey('error', $result);
    }

    public function test_is_valid_returns_false_with_no_key(): void
    {
        config(['dravion.license_key' => '']);

        $this->assertFalse(LicenseService::isValid());
    }

    public function test_is_valid_returns_true_for_dev_key_on_localhost(): void
    {
        config(['dravion.license_key' => 'DEV-TEST', 'app.url' => 'http://localhost']);

        $this->assertTrue(LicenseService::isValid());
    }

    public function test_is_valid_returns_false_for_dev_key_on_production_domain(): void
    {
        config(['dravion.license_key' => 'DEV-TEST', 'app.url' => 'https://example.com']);

        $this->assertFalse(LicenseService::isValid());
    }
}
