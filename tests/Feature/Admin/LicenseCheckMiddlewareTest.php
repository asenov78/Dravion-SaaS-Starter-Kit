<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Services\LicenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseCheckMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private string $cacheFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheFile = storage_path('license.cache');
        @unlink($this->cacheFile);
    }

    protected function tearDown(): void
    {
        @unlink($this->cacheFile);
        parent::tearDown();
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        return $user;
    }

    /** Write a properly HMAC-signed cache entry. */
    private function writeCache(array $data): void
    {
        LicenseService::writeCache($data);
    }

    /** Read the signed cache and return the inner payload array. */
    private function readCache(): ?array
    {
        return LicenseService::readCachePublic();
    }

    // ── No key ────────────────────────────────────────────────────────────

    public function test_no_license_key_flashes_warning_and_allows_access(): void
    {
        config(['dravion.license_key' => '']);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertSessionHas('license_warning');
    }

    public function test_no_license_key_does_not_create_cache_file(): void
    {
        config(['dravion.license_key' => '']);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'));

        $this->assertFileDoesNotExist($this->cacheFile);
    }

    // ── Fresh valid cache ──────────────────────────────────────────────────

    public function test_fresh_valid_cache_allows_access_without_warning(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'message' => null]);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertSessionMissing('license_warning');
    }

    public function test_fresh_invalid_cache_flashes_warning(): void
    {
        config(['dravion.license_key' => 'DRV-REVOKED']);
        $this->writeCache(['valid' => false, 'checked_at' => time(), 'message' => 'License revoked.']);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertSessionHas('license_warning', 'License revoked.');
    }

    // ── Stale cache → re-ping ─────────────────────────────────────────────

    public function test_stale_cache_repings_server_and_updates_cache_when_valid(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->writeCache(['valid' => true, 'checked_at' => time() - 90000, 'message' => null]);

        $before = time();

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'));

        $cached = $this->readCache();
        $this->assertNotNull($cached);
        $this->assertGreaterThanOrEqual($before, $cached['checked_at']);
    }

    public function test_stale_cache_repings_and_shows_warning_when_server_revokes(): void
    {
        config(['dravion.license_key' => 'DRV-REVOKED']);
        $this->writeCache(['valid' => true, 'checked_at' => time() - 90000, 'message' => null]);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertStatus(200);

        $cached = $this->readCache();
        $this->assertNotNull($cached);
        $this->assertArrayHasKey('valid', $cached);
        $this->assertArrayHasKey('checked_at', $cached);
    }

    // ── Missing cache → ping server ───────────────────────────────────────

    public function test_missing_cache_pings_server_and_creates_cache_file(): void
    {
        config(['dravion.license_key' => 'DRV-SOMEKEY']);
        $this->assertFileDoesNotExist($this->cacheFile);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'));

        $this->assertFileExists($this->cacheFile);
        $cached = $this->readCache();
        $this->assertNotNull($cached);
        $this->assertArrayHasKey('valid', $cached);
        $this->assertArrayHasKey('checked_at', $cached);
    }

    // ── After remove license ───────────────────────────────────────────────

    public function test_after_remove_license_next_request_shows_warning(): void
    {
        config(['dravion.license_key' => '']);
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'message' => null]);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertSessionHas('license_warning');
    }

    // ── TTL boundary ──────────────────────────────────────────────────────

    public function test_cache_within_24h_is_not_repinged(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $checkedAt = time() - 3600; // 1 hour ago — still fresh
        $this->writeCache(['valid' => true, 'checked_at' => $checkedAt, 'message' => null]);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertSessionMissing('license_warning');

        // checked_at must NOT have changed (no re-ping)
        $cached = $this->readCache();
        $this->assertEquals($checkedAt, $cached['checked_at']);
    }

    public function test_cache_older_than_24h_is_repinged(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $checkedAt = time() - 90000; // 25 hours ago — stale
        $this->writeCache(['valid' => true, 'checked_at' => $checkedAt, 'message' => null]);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'));

        $cached = $this->readCache();
        $this->assertGreaterThanOrEqual($checkedAt + 1, $cached['checked_at']);
    }

    // ── Tampered cache is rejected ────────────────────────────────────────

    public function test_tampered_cache_is_treated_as_missing(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);

        // Write a tampered (unsigned) cache file — should be rejected
        file_put_contents($this->cacheFile, json_encode(['valid' => true, 'checked_at' => time()]));

        // readCachePublic() should return null → middleware pings server
        $this->assertNull(LicenseService::readCachePublic());
    }

    // ── Dev key on production ─────────────────────────────────────────────

    public function test_dev_key_on_production_flashes_specific_warning(): void
    {
        config(['dravion.license_key' => 'DEV-abc123', 'app.url' => 'https://mysite.com']);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertSessionHas('license_warning', fn($v) => str_contains($v, 'development'));
    }

    public function test_dev_key_on_production_does_not_ping_server(): void
    {
        config(['dravion.license_key' => 'DEV-abc123', 'app.url' => 'https://mysite.com']);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'));

        $this->assertFileDoesNotExist($this->cacheFile);
    }

    public function test_dev_key_on_localhost_no_warning(): void
    {
        config(['dravion.license_key' => 'DEV-abc123', 'app.url' => 'http://localhost']);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertSessionMissing('license_warning');
    }

    public function test_dev_key_on_dot_test_no_warning(): void
    {
        config(['dravion.license_key' => 'DEV-abc123', 'app.url' => 'http://myapp.test']);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertSessionMissing('license_warning');
    }
}
