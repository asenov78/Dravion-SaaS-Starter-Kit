<?php

namespace Tests\Unit;

use App\Contracts\LicenseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LicenseServiceTest extends TestCase
{
    use RefreshDatabase;

    private function svc(): LicenseServiceInterface
    {
        return app(LicenseServiceInterface::class);
    }

    public function test_activate_returns_license_key_on_success(): void
    {
        Http::fake([
            '*activate*' => Http::response(['license_key' => 'DRV-TESTKEY123'], 200),
        ]);

        $result = $this->svc()->activate('PURCHASE-CODE-123', 'example.com');

        $this->assertArrayHasKey('license_key', $result);
        $this->assertSame('DRV-TESTKEY123', $result['license_key']);
        $this->assertArrayNotHasKey('error', $result);
    }

    public function test_activate_returns_error_on_server_failure(): void
    {
        Http::fake([
            '*activate*' => Http::response(['error' => 'Invalid purchase code'], 422),
        ]);

        $result = $this->svc()->activate('BAD-CODE', 'example.com');

        $this->assertArrayHasKey('error', $result);
        $this->assertSame('Invalid purchase code', $result['error']);
    }

    public function test_activate_returns_error_on_network_failure(): void
    {
        Http::fake([
            '*activate*' => fn() => throw new \Illuminate\Http\Client\ConnectionException('timeout'),
        ]);

        $result = $this->svc()->activate('CODE', 'example.com');

        $this->assertArrayHasKey('error', $result);
    }

    public function test_is_valid_returns_false_with_no_key(): void
    {
        config(['dravion.license_key' => '']);

        $this->assertFalse($this->svc()->isValid());
    }

    public function test_is_valid_returns_true_for_dev_key_on_localhost(): void
    {
        config(['dravion.license_key' => 'DEV-TEST', 'app.url' => 'http://localhost']);

        $this->assertTrue($this->svc()->isValid());
    }

    public function test_is_valid_returns_false_for_dev_key_on_production_domain(): void
    {
        config(['dravion.license_key' => 'DEV-TEST', 'app.url' => 'https://example.com']);

        $this->assertFalse($this->svc()->isValid());
    }
}
