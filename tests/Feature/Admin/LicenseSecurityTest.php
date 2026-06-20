<?php

namespace Tests\Feature\Admin;

use App\Contracts\LicenseServiceInterface;
use App\Models\User;
use App\Services\LicenseService;
use App\Services\UpdaterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Comprehensive license security tests.
 *
 * Covers every scenario that could allow an update to be installed
 * on a suspended / revoked / missing / tampered license.
 */
class LicenseSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        @unlink(storage_path('license.cache'));
        config(['updater.owner' => 'acme', 'updater.repo' => 'app']);
    }

    protected function tearDown(): void
    {
        @unlink(storage_path('license.cache'));
        parent::tearDown();
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    private function svc(): LicenseServiceInterface
    {
        return app(LicenseServiceInterface::class);
    }

    /** Fake the license server to return a specific status. */
    private function fakeServer(bool $valid, string $status = '', ?string $message = null): void
    {
        Http::fake([
            '*validate*'  => Http::response([
                'valid'   => $valid,
                'status'  => $status ?: ($valid ? 'active' : 'suspended'),
                'message' => $message ?? ($valid ? null : 'License suspended.'),
            ], 200),
            '*activate*'  => Http::response(['license_key' => 'DRV-ACTIVATED'], 200),
            'api.github.com/*' => Http::response([], 200),
        ]);
    }

    /** Fake server as completely unreachable. */
    private function fakeServerDown(): void
    {
        Http::fake([
            '*validate*' => fn() => throw new \Illuminate\Http\Client\ConnectionException('timeout'),
            '*activate*' => fn() => throw new \Illuminate\Http\Client\ConnectionException('timeout'),
            'api.github.com/*' => Http::response([], 200),
        ]);
    }

    private function validZipUrl(): string
    {
        return 'https://api.github.com/repos/acme/app/zipball/v1.3.0';
    }

    private function writeCache(array $data): void
    {
        $this->svc()->writeCache($data);
    }

    // ════════════════════════════════════════════════════════════════════════
    // SECTION 1 — isValid() (cache-based)
    // ════════════════════════════════════════════════════════════════════════

    public function test_is_valid_false_with_empty_key(): void
    {
        config(['dravion.license_key' => '']);
        $this->assertFalse($this->svc()->isValid());
    }

    public function test_is_valid_false_without_cache(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->assertFalse($this->svc()->isValid());
    }

    public function test_is_valid_true_with_valid_cache(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);
        $this->assertTrue($this->svc()->isValid());
    }

    public function test_is_valid_false_with_suspended_cache(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->writeCache(['valid' => false, 'checked_at' => time(), 'status' => 'suspended']);
        $this->assertFalse($this->svc()->isValid());
    }

    public function test_is_valid_false_with_tampered_cache(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        file_put_contents(storage_path('license.cache'), json_encode(['valid' => true, 'checked_at' => time()]));
        $this->assertFalse($this->svc()->isValid());
    }

    public function test_is_valid_true_for_dev_key_on_localhost(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'http://localhost']);
        $this->assertTrue($this->svc()->isValid());
    }

    public function test_is_valid_true_for_dev_key_on_dot_test(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'http://myapp.test']);
        $this->assertTrue($this->svc()->isValid());
    }

    public function test_is_valid_true_for_dev_key_on_dot_local(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'http://myapp.local']);
        $this->assertTrue($this->svc()->isValid());
    }

    public function test_is_valid_false_for_dev_key_on_production(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'https://mysite.com']);
        $this->assertFalse($this->svc()->isValid());
    }

    // ════════════════════════════════════════════════════════════════════════
    // SECTION 2 — verifyNow() (live ping)
    // ════════════════════════════════════════════════════════════════════════

    public function test_verify_now_returns_false_for_empty_key(): void
    {
        config(['dravion.license_key' => '']);
        $result = $this->svc()->verifyNow();
        $this->assertFalse($result['valid']);
        $this->assertSame('missing', $result['status']);
    }

    public function test_verify_now_returns_active_for_dev_key_on_dev_domain(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'http://localhost']);
        $result = $this->svc()->verifyNow();
        $this->assertTrue($result['valid']);
        $this->assertSame('active', $result['status']);
    }

    public function test_verify_now_returns_invalid_for_dev_key_on_production(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'https://mysite.com']);
        $result = $this->svc()->verifyNow();
        $this->assertFalse($result['valid']);
        $this->assertSame('dev_key_on_prod', $result['status']);
    }

    public function test_verify_now_pings_server_and_returns_active(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->fakeServer(true, 'active');

        $result = $this->svc()->verifyNow();

        $this->assertTrue($result['valid']);
        $this->assertSame('active', $result['status']);
        Http::assertSent(fn($r) => str_contains($r->url(), 'validate'));
    }

    public function test_verify_now_pings_server_and_returns_suspended(): void
    {
        config(['dravion.license_key' => 'DRV-SUSPENDED']);
        $this->fakeServer(false, 'suspended', 'License suspended.');

        $result = $this->svc()->verifyNow();

        $this->assertFalse($result['valid']);
        $this->assertSame('suspended', $result['status']);
        $this->assertSame('License suspended.', $result['message']);
    }

    public function test_verify_now_pings_server_and_returns_revoked(): void
    {
        config(['dravion.license_key' => 'DRV-REVOKED']);
        $this->fakeServer(false, 'revoked', 'License revoked.');

        $result = $this->svc()->verifyNow();

        $this->assertFalse($result['valid']);
        $this->assertSame('revoked', $result['status']);
    }

    public function test_verify_now_updates_cache_after_ping(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->fakeServer(true, 'active');

        $before = time();
        $this->svc()->verifyNow();

        $cached = $this->svc()->readCachePublic();
        $this->assertNotNull($cached);
        $this->assertTrue($cached['valid']);
        $this->assertGreaterThanOrEqual($before, $cached['checked_at']);
    }

    public function test_verify_now_writes_suspended_to_cache(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        // Write valid cache first (simulating: was active, now suspended)
        $this->writeCache(['valid' => true, 'checked_at' => time() - 100, 'status' => 'active']);
        $this->fakeServer(false, 'suspended', 'Suspended.');

        $this->svc()->verifyNow();

        $cached = $this->svc()->readCachePublic();
        $this->assertFalse($cached['valid']); // cache now reflects suspension
    }

    public function test_verify_now_server_unreachable_fails_open_when_cache_valid(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->writeCache(['valid' => true, 'checked_at' => time() - 100, 'status' => 'active']);
        $this->fakeServerDown();

        $result = $this->svc()->verifyNow();

        $this->assertTrue($result['valid']); // fail-open: cached=true → return true
        $this->assertSame('server_unreachable', $result['status']);
    }

    public function test_verify_now_server_unreachable_fails_closed_when_cache_invalid(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->writeCache(['valid' => false, 'checked_at' => time() - 100, 'status' => 'suspended']);
        $this->fakeServerDown();

        $result = $this->svc()->verifyNow();

        $this->assertFalse($result['valid']); // no valid cache → false
        $this->assertSame('server_unreachable', $result['status']);
    }

    public function test_verify_now_server_unreachable_no_cache_returns_false(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->fakeServerDown();

        $result = $this->svc()->verifyNow();

        $this->assertFalse($result['valid']); // no cache at all
    }

    // ════════════════════════════════════════════════════════════════════════
    // SECTION 3 — isValidLive()
    // ════════════════════════════════════════════════════════════════════════

    public function test_is_valid_live_false_for_empty_key(): void
    {
        config(['dravion.license_key' => '']);
        $this->assertFalse($this->svc()->isValidLive());
    }

    public function test_is_valid_live_true_for_dev_key_on_dev_domain(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'http://localhost']);
        $this->assertTrue($this->svc()->isValidLive());
    }

    public function test_is_valid_live_false_for_dev_key_on_production(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'https://mysite.com']);
        $this->assertFalse($this->svc()->isValidLive());
    }

    public function test_is_valid_live_true_when_server_confirms_active(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->fakeServer(true, 'active');
        $this->assertTrue($this->svc()->isValidLive());
    }

    public function test_is_valid_live_false_when_server_returns_suspended(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        // Cache says valid (was active) but server now says suspended
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);
        $this->fakeServer(false, 'suspended', 'Suspended.');

        // isValid() still returns true (reads cache)
        $this->assertTrue($this->svc()->isValid());
        // isValidLive() catches the suspension
        $this->assertFalse($this->svc()->isValidLive());
    }

    public function test_is_valid_live_false_when_server_returns_revoked(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);
        $this->fakeServer(false, 'revoked', 'Revoked.');

        $this->assertFalse($this->svc()->isValidLive());
    }

    public function test_is_valid_live_does_not_rely_on_stale_cache(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        // Cache is valid and fresh → isValid() would return true
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);
        // But server now says suspended
        $this->fakeServer(false, 'suspended', 'Suspended.');

        // isValidLive() must ping server and catch it
        $this->assertFalse($this->svc()->isValidLive());
        // And update the cache
        $cached = $this->svc()->readCachePublic();
        $this->assertFalse($cached['valid']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // SECTION 4 — THE CRITICAL SCENARIO: activate → suspend → try to install
    // ════════════════════════════════════════════════════════════════════════

    public function test_activate_then_suspend_then_install_is_blocked(): void
    {
        // Step 1: license was active, cache reflects that
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);

        // Step 2: license gets suspended on the server
        $this->fakeServer(false, 'suspended', 'License suspended.');

        // Step 3: attempt to install — must be blocked
        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertStatus(403);
    }

    public function test_activate_then_suspend_then_check_hides_zip_url(): void
    {
        // Cache: valid (was active). Server: suspended.
        config(['dravion.license_key' => 'DRV-VALID', 'dravion.version' => '1.0.0']);
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);

        Http::fake([
            '*validate*'       => Http::response(['valid' => false, 'status' => 'suspended'], 200),
            'api.github.com/*' => Http::response([[
                'tag_name'    => 'v1.1.0',
                'body'        => 'Notes',
                'zipball_url' => $this->validZipUrl(),
                'draft'       => false,
            ]], 200),
        ]);

        $response = $this->actingAs($this->admin())
            ->getJson('/admin/updates/check')
            ->assertOk()
            ->json();

        $this->assertNull($response['zip_url']);
        foreach ($response['newer'] ?? [] as $release) {
            $this->assertNull($release['zip_url']);
        }
    }

    public function test_activate_then_revoke_then_install_is_blocked(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);
        $this->fakeServer(false, 'revoked', 'License revoked.');

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertStatus(403);
    }

    public function test_remove_license_then_install_is_blocked(): void
    {
        config(['dravion.license_key' => '']);
        // Even with a valid cache from before, empty key must block
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertStatus(403);
    }

    // ════════════════════════════════════════════════════════════════════════
    // SECTION 5 — UpdateController::check() endpoint
    // ════════════════════════════════════════════════════════════════════════

    public function test_check_endpoint_shows_zip_url_when_license_active(): void
    {
        config(['dravion.license_key' => 'DRV-VALID', 'dravion.version' => '1.0.0']);

        Http::fake([
            '*validate*'       => Http::response(['valid' => true, 'status' => 'active'], 200),
            'api.github.com/*' => Http::response([[
                'tag_name'    => 'v1.1.0',
                'body'        => 'Notes',
                'zipball_url' => $this->validZipUrl(),
                'draft'       => false,
            ]], 200),
        ]);

        $response = $this->actingAs($this->admin())
            ->getJson('/admin/updates/check')
            ->assertOk()
            ->json();

        $this->assertNotNull($response['zip_url']);
    }

    public function test_check_endpoint_hides_zip_url_when_no_license(): void
    {
        config(['dravion.license_key' => '', 'dravion.version' => '1.0.0']);
        Http::fake(['api.github.com/*' => Http::response([[
            'tag_name' => 'v1.1.0', 'body' => '', 'zipball_url' => $this->validZipUrl(), 'draft' => false,
        ]], 200)]);

        $response = $this->actingAs($this->admin())
            ->getJson('/admin/updates/check')
            ->assertOk()
            ->json();

        $this->assertNull($response['zip_url']);
    }

    public function test_check_endpoint_hides_zip_url_when_license_suspended(): void
    {
        config(['dravion.license_key' => 'DRV-VALID', 'dravion.version' => '1.0.0']);
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);

        Http::fake([
            '*validate*'       => Http::response(['valid' => false, 'status' => 'suspended'], 200),
            'api.github.com/*' => Http::response([[
                'tag_name' => 'v1.1.0', 'body' => '', 'zipball_url' => $this->validZipUrl(), 'draft' => false,
            ]], 200),
        ]);

        $response = $this->actingAs($this->admin())
            ->getJson('/admin/updates/check')
            ->assertOk()
            ->json();

        $this->assertNull($response['zip_url']);
    }

    public function test_check_endpoint_hides_zip_url_for_each_release_when_suspended(): void
    {
        config(['dravion.license_key' => 'DRV-VALID', 'dravion.version' => '1.0.0']);
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);

        Http::fake([
            '*validate*' => Http::response(['valid' => false, 'status' => 'suspended'], 200),
            'api.github.com/*' => Http::response([
                ['tag_name' => 'v1.2.0', 'body' => '', 'zipball_url' => 'https://api.github.com/repos/acme/app/zipball/v1.2.0', 'draft' => false],
                ['tag_name' => 'v1.1.0', 'body' => '', 'zipball_url' => 'https://api.github.com/repos/acme/app/zipball/v1.1.0', 'draft' => false],
            ], 200),
        ]);

        $response = $this->actingAs($this->admin())->getJson('/admin/updates/check')->json();

        foreach ($response['newer'] ?? [] as $release) {
            $this->assertNull($release['zip_url'], "zip_url must be null for release {$release['version']}");
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    // SECTION 6 — UpdateController::install() endpoint
    // ════════════════════════════════════════════════════════════════════════

    public function test_install_blocked_with_no_license(): void
    {
        config(['dravion.license_key' => '']);

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertStatus(403);
    }

    public function test_install_blocked_when_server_says_suspended(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->fakeServer(false, 'suspended', 'Suspended.');

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertStatus(403);
    }

    public function test_install_blocked_when_server_says_revoked(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->fakeServer(false, 'revoked', 'Revoked.');

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertStatus(403);
    }

    public function test_install_allowed_when_server_confirms_active(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->fakeServer(true, 'active');

        $mock = $this->mock(UpdaterService::class);
        $mock->shouldReceive('downloadAndInstall')
            ->once()
            ->andReturn(['ok' => true, 'message' => 'done', 'version' => '1.1.0']);

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_install_blocked_for_dev_key_on_production(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'https://mysite.com']);

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertStatus(403);
    }

    public function test_install_allowed_for_dev_key_on_dev_domain(): void
    {
        config(['dravion.license_key' => 'DEV-LOCAL', 'app.url' => 'http://localhost']);

        $mock = $this->mock(UpdaterService::class);
        $mock->shouldReceive('downloadAndInstall')
            ->once()
            ->andReturn(['ok' => true, 'message' => 'done', 'version' => '1.1.0']);

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertOk();
    }

    public function test_install_server_unreachable_uses_cached_valid_to_proceed(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        // Valid cache — server unreachable → fail-open → allowed
        $this->writeCache(['valid' => true, 'checked_at' => time(), 'status' => 'active']);
        $this->fakeServerDown();

        $mock = $this->mock(UpdaterService::class);
        $mock->shouldReceive('downloadAndInstall')
            ->once()
            ->andReturn(['ok' => true, 'message' => 'done', 'version' => '1.1.0']);

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertOk();
    }

    public function test_install_server_unreachable_with_invalid_cache_is_blocked(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        // Suspended in cache — server unreachable → stays blocked
        $this->writeCache(['valid' => false, 'checked_at' => time(), 'status' => 'suspended']);
        $this->fakeServerDown();

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertStatus(403);
    }

    public function test_install_server_unreachable_no_cache_is_blocked(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        // No cache + server down → blocked (not fail-open when no prior state)
        $this->fakeServerDown();

        $this->actingAs($this->admin())
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertStatus(403);
    }

    public function test_install_rejects_non_github_zip_url(): void
    {
        config(['dravion.license_key' => 'DRV-VALID']);
        $this->fakeServer(true, 'active');

        $rule  = new \App\Rules\GitHubZipUrl;
        $failed = false;
        $rule->validate('zip_url', 'https://evil.com/malware.zip', function () use (&$failed) {
            $failed = true;
        });
        $this->assertTrue($failed);
    }

    // ════════════════════════════════════════════════════════════════════════
    // SECTION 7 — activate() method
    // ════════════════════════════════════════════════════════════════════════

    public function test_activate_returns_license_key_on_success(): void
    {
        Http::fake(['*activate*' => Http::response(['license_key' => 'DRV-TESTKEY'], 200)]);

        $result = $this->svc()->activate('PURCHASE-123', 'example.com');

        $this->assertArrayHasKey('license_key', $result);
        $this->assertSame('DRV-TESTKEY', $result['license_key']);
    }

    public function test_activate_returns_error_on_server_rejection(): void
    {
        Http::fake(['*activate*' => Http::response(['error' => 'Invalid purchase code.'], 422)]);

        $result = $this->svc()->activate('BAD-CODE', 'example.com');

        $this->assertArrayHasKey('error', $result);
        $this->assertSame('Invalid purchase code.', $result['error']);
    }

    public function test_activate_returns_error_on_network_failure(): void
    {
        Http::fake(['*activate*' => fn() => throw new \Illuminate\Http\Client\ConnectionException('timeout')]);

        $result = $this->svc()->activate('CODE', 'example.com');

        $this->assertArrayHasKey('error', $result);
    }

    public function test_activate_returns_error_when_response_missing_license_key(): void
    {
        Http::fake(['*activate*' => Http::response(['ok' => true], 200)]);

        $result = $this->svc()->activate('CODE', 'example.com');

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Invalid response', $result['error']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // SECTION 8 — Cache integrity
    // ════════════════════════════════════════════════════════════════════════

    public function test_write_cache_produces_hmac_signed_file(): void
    {
        $this->svc()->writeCache(['valid' => true, 'checked_at' => time()]);

        $raw = json_decode(file_get_contents(storage_path('license.cache')), true);
        $this->assertArrayHasKey('payload', $raw);
        $this->assertArrayHasKey('sig', $raw);
    }

    public function test_read_cache_rejects_tampered_payload(): void
    {
        $this->svc()->writeCache(['valid' => true, 'checked_at' => time()]);

        // Tamper the payload
        $raw = json_decode(file_get_contents(storage_path('license.cache')), true);
        $inner = json_decode($raw['payload'], true);
        $inner['valid'] = false;
        $raw['payload'] = json_encode($inner); // payload changed but sig not
        file_put_contents(storage_path('license.cache'), json_encode($raw));

        $this->assertNull($this->svc()->readCachePublic());
    }

    public function test_read_cache_rejects_truncated_file(): void
    {
        file_put_contents(storage_path('license.cache'), 'not-json');
        $this->assertNull($this->svc()->readCachePublic());
    }

    public function test_read_cache_returns_null_when_file_missing(): void
    {
        $this->assertNull($this->svc()->readCachePublic());
    }

    // ════════════════════════════════════════════════════════════════════════
    // SECTION 9 — Access control (non-admin cannot touch update endpoints)
    // ════════════════════════════════════════════════════════════════════════

    public function test_non_admin_cannot_reach_install_endpoint(): void
    {
        $user = User::factory()->create();
        $user->assignRole('manager');

        $this->actingAs($user)
            ->postJson('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertStatus(403);
    }

    public function test_guest_cannot_reach_install_endpoint(): void
    {
        $this->post('/admin/updates/install', ['zip_url' => $this->validZipUrl()])
            ->assertRedirect('/login');
    }

    public function test_non_admin_cannot_reach_check_endpoint(): void
    {
        $user = User::factory()->create();
        $user->assignRole('manager');

        $this->actingAs($user)
            ->getJson('/admin/updates/check')
            ->assertStatus(403);
    }
}
