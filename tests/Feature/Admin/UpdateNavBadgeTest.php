<?php

namespace Tests\Feature\Admin;

use App\Helpers\MenuHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdateNavBadgeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('github_latest_version');
        Cache::forget('github_check_failed');
        // Disable auto-fetch by default — individual tests opt-in via Http::fake
        config(['updater.owner' => '', 'updater.repo' => '']);
    }

    protected function tearDown(): void
    {
        Cache::forget('github_latest_version');
        Cache::forget('github_check_failed');
        parent::tearDown();
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    private function updatesItem(): ?array
    {
        foreach (MenuHelper::getSystemItems() as $item) {
            if (($item['route'] ?? '') === 'admin.updates') {
                return $item;
            }
        }
        return null;
    }

    // --- MenuHelper unit ---

    public function test_no_badge_when_cache_empty(): void
    {
        $this->actingAs($this->admin());
        $item = $this->updatesItem();

        $this->assertNotNull($item);
        $this->assertArrayNotHasKey('update_available', $item);
    }

    public function test_no_badge_when_same_version(): void
    {
        $current = ltrim(config('dravion.version', '0.0.0'), 'v');
        Cache::put('github_latest_version', $current, 3600);

        $this->actingAs($this->admin());
        $item = $this->updatesItem();

        $this->assertArrayNotHasKey('update_available', $item);
    }

    public function test_no_badge_when_cached_version_is_older(): void
    {
        Cache::put('github_latest_version', '0.0.1', 3600);

        $this->actingAs($this->admin());
        $item = $this->updatesItem();

        $this->assertArrayNotHasKey('update_available', $item);
    }

    public function test_update_available_set_when_newer_version_cached(): void
    {
        Cache::put('github_latest_version', '99.0.0', 3600);

        $this->actingAs($this->admin());
        $item = $this->updatesItem();

        $this->assertTrue($item['update_available'] ?? false);
    }

    public function test_update_available_with_v_prefix_in_cache(): void
    {
        Cache::put('github_latest_version', 'v99.0.0', 3600);

        $this->actingAs($this->admin());
        $item = $this->updatesItem();

        $this->assertTrue($item['update_available'] ?? false);
    }

    public function test_updates_item_absent_for_non_admin(): void
    {
        Cache::put('github_latest_version', '99.0.0', 3600);

        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $this->actingAs($manager);

        $this->assertNull($this->updatesItem());
    }

    // --- Blade rendering ---

    public function test_sidebar_shows_update_badge_when_newer_version_available(): void
    {
        Cache::put('github_latest_version', '99.0.0', 3600);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('data-update-badge="1"', $html);
        $this->assertStringContainsString('UPDATE', $html);
    }

    public function test_sidebar_has_no_update_badge_when_up_to_date(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('data-update-badge="1"', $html);
    }

    // --- auto-fetch tests ---

    public function test_badge_auto_fetches_from_github_when_cache_empty(): void
    {
        config(['updater.owner' => 'o', 'updater.repo' => 'r']);
        Http::fake([
            'api.github.com/*' => Http::response([[
                'tag_name'    => 'v99.0.0',
                'body'        => 'notes',
                'zipball_url' => 'https://api.github.com/repos/o/r/zipball/v99.0.0',
                'draft'       => false,
                'prerelease'  => false,
            ]], 200),
        ]);

        $this->actingAs($this->admin());
        $item = $this->updatesItem();

        $this->assertTrue($item['update_available'] ?? false);
        // Also written to cache for subsequent requests
        $this->assertSame('99.0.0', Cache::get('github_latest_version'));
    }

    public function test_no_badge_when_github_unreachable_and_cache_empty(): void
    {
        config(['updater.owner' => 'o', 'updater.repo' => 'r']);
        Http::fake(['api.github.com/*' => Http::response(null, 503)]);

        $this->actingAs($this->admin());
        $item = $this->updatesItem();

        $this->assertArrayNotHasKey('update_available', $item);
        // Failed check is cached briefly to avoid hammering GitHub
        $this->assertTrue((bool) Cache::get('github_check_failed'));
    }

    public function test_no_auto_fetch_when_owner_not_configured(): void
    {
        config(['updater.owner' => '', 'updater.repo' => '']);

        $this->actingAs($this->admin());
        $item = $this->updatesItem();

        $this->assertArrayNotHasKey('update_available', $item);
    }
}
