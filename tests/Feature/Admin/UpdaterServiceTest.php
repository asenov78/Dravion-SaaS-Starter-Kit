<?php

namespace Tests\Feature\Admin;

use App\Services\UpdaterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdaterServiceTest extends TestCase
{
    use RefreshDatabase;

    private function svc(): UpdaterService
    {
        return app(UpdaterService::class);
    }

    private function fakeRelease(string $tag, ?string $requires = null): void
    {
        $this->fakeReleases([[$tag, $requires]]);
    }

    /**
     * @param array<int, string|array{0:string, 1:string|null}> $tags
     *   Each element is either a tag string or [tag, requires].
     *   Tags should be newest first.
     */
    private function fakeReleases(array $tags): void
    {
        Http::fake([
            'api.github.com/*' => Http::response(array_map(function ($entry) {
                [$tag, $requires] = is_array($entry) ? $entry : [$entry, null];
                $body = "## What's new in {$tag}\n- Stuff";
                if ($requires !== null) {
                    $body = "requires: {$requires}\n" . $body;
                }
                return [
                    'tag_name'    => $tag,
                    'body'        => $body,
                    'zipball_url' => 'https://api.github.com/repos/o/r/zipball/' . $tag,
                    'draft'       => false,
                    'prerelease'  => false,
                ];
            }, $tags), 200),
        ]);
        config(['updater.owner' => 'o', 'updater.repo' => 'r', 'updater.token' => '']);
    }

    public function test_get_current_version_reads_config(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->assertSame('1.2.29', $this->svc()->getCurrentVersion());
    }

    public function test_check_detects_newer_version(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->fakeRelease('v1.3.0');

        $result = $this->svc()->checkForUpdate();

        $this->assertTrue($result['has_update']);
        $this->assertSame('1.3.0', $result['latest']);
        $this->assertSame('1.2.29', $result['current']);
        $this->assertStringContainsString('What', $result['changelog']);
        $this->assertNotEmpty($result['zip_url']);
    }

    public function test_check_returns_changelog_for_each_newer_version(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->fakeReleases(['v1.4.0', 'v1.3.0', 'v1.2.0']);

        $result = $this->svc()->checkForUpdate();

        // Only versions newer than current, newest first.
        $versions = array_column($result['newer'], 'version');
        $this->assertSame(['1.4.0', '1.3.0'], $versions);

        $this->assertStringContainsString('v1.4.0', $result['newer'][0]['changelog']);
        $this->assertStringContainsString('v1.3.0', $result['newer'][1]['changelog']);
        $this->assertSame('1.4.0', $result['latest']);
        $this->assertTrue($result['has_update']);
    }

    public function test_newer_is_empty_when_up_to_date(): void
    {
        config(['dravion.version' => '1.4.0']);
        $this->fakeReleases(['v1.4.0', 'v1.3.0']);

        $this->assertSame([], $this->svc()->checkForUpdate()['newer']);
    }

    public function test_check_no_update_when_same_version(): void
    {
        config(['dravion.version' => '1.3.0']);
        $this->fakeRelease('v1.3.0');

        $result = $this->svc()->checkForUpdate();

        $this->assertFalse($result['has_update']);
    }

    public function test_check_no_update_when_current_is_newer(): void
    {
        config(['dravion.version' => '1.4.0']);
        $this->fakeRelease('v1.3.0');

        $this->assertFalse($this->svc()->checkForUpdate()['has_update']);
    }

    public function test_check_handles_github_error_gracefully(): void
    {
        config(['dravion.version' => '1.2.29']);
        Http::fake(['api.github.com/*' => Http::response('nope', 404)]);
        config(['updater.owner' => 'o', 'updater.repo' => 'r']);

        $result = $this->svc()->checkForUpdate();

        $this->assertFalse($result['has_update']);
        $this->assertNull($result['latest']);
    }

    // --- requires / sequential chain tests ---

    public function test_release_with_requires_parsed_from_body(): void
    {
        config(['dravion.version' => '1.0.0']);
        $this->fakeRelease('v1.0.1', '1.0.0');

        $releases = $this->svc()->getReleases();

        $this->assertSame('1.0.0', $releases[0]['requires']);
    }

    public function test_release_without_requires_has_null(): void
    {
        config(['dravion.version' => '1.0.0']);
        $this->fakeRelease('v1.0.1');

        $releases = $this->svc()->getReleases();

        $this->assertNull($releases[0]['requires']);
    }

    public function test_release_not_blocked_when_requires_satisfied(): void
    {
        // current=1.0.1, release requires 1.0.1 — satisfied
        config(['dravion.version' => '1.0.1']);
        $this->fakeRelease('v1.0.2', '1.0.1');

        $result = $this->svc()->checkForUpdate();

        $this->assertFalse($result['newer'][0]['blocked']);
        $this->assertNotNull($result['next_installable']);
        $this->assertSame('1.0.2', $result['next_installable']['version']);
    }

    public function test_release_blocked_when_requires_not_satisfied(): void
    {
        // current=1.0.0, release requires 1.0.1 which is not installed
        config(['dravion.version' => '1.0.0']);
        $this->fakeRelease('v1.0.2', '1.0.1');

        $result = $this->svc()->checkForUpdate();

        $this->assertTrue($result['newer'][0]['blocked']);
        $this->assertNull($result['next_installable']);
    }

    public function test_chain_blocked_when_middle_version_missing(): void
    {
        // current=1.0.0; releases available: 1.0.3 (requires 1.0.2), 1.0.1 (no requires)
        // 1.0.2 is missing from GitHub → 1.0.3 is blocked, 1.0.1 is installable
        config(['dravion.version' => '1.0.0']);
        $this->fakeReleases([
            ['v1.0.3', '1.0.2'],  // requires 1.0.2 — not available
            ['v1.0.1', null],     // no requires — installable
        ]);

        $result = $this->svc()->checkForUpdate();

        // 1.0.1 should be installable (oldest non-blocked)
        $this->assertNotNull($result['next_installable']);
        $this->assertSame('1.0.1', $result['next_installable']['version']);
        $this->assertFalse($result['next_installable']['blocked']);

        // 1.0.3 should be blocked
        $newer = array_column($result['newer'], null, 'version');
        $this->assertTrue($newer['1.0.3']['blocked']);
        $this->assertFalse($newer['1.0.1']['blocked']);
    }

    public function test_all_releases_blocked_when_chain_starts_broken(): void
    {
        // current=1.0.0; both releases require something newer than current
        config(['dravion.version' => '1.0.0']);
        $this->fakeReleases([
            ['v1.0.3', '1.0.2'],
            ['v1.0.2', '1.0.1'],  // 1.0.1 not released
        ]);

        $result = $this->svc()->checkForUpdate();

        $this->assertNull($result['next_installable']);
        foreach ($result['newer'] as $rel) {
            $this->assertTrue($rel['blocked'], "Expected {$rel['version']} to be blocked");
        }
    }

    public function test_requires_with_v_prefix_parsed_correctly(): void
    {
        config(['dravion.version' => '1.0.0']);
        // body has "requires: v1.0.0" (with v prefix)
        Http::fake([
            'api.github.com/*' => Http::response([[
                'tag_name'    => 'v1.0.1',
                'body'        => "requires: v1.0.0\nSome changes",
                'zipball_url' => 'https://api.github.com/repos/o/r/zipball/v1.0.1',
                'draft'       => false,
                'prerelease'  => false,
            ]], 200),
        ]);
        config(['updater.owner' => 'o', 'updater.repo' => 'r', 'updater.token' => '']);

        $releases = $this->svc()->getReleases();
        $this->assertSame('1.0.0', $releases[0]['requires']); // v prefix stripped
    }

    // --- asset URL preference tests ---

    public function test_prefers_release_asset_zip_over_zipball_url(): void
    {
        // The CI workflow builds dravion-vX.Y.Z.zip with npm run build + no dev deps.
        // zipball_url is the raw git source and does NOT include public/build (gitignored).
        // The updater must prefer the release asset over zipball_url.
        config(['dravion.version' => '1.0.0', 'updater.owner' => 'o', 'updater.repo' => 'r', 'updater.token' => '']);

        $assetUrl = 'https://github.com/o/r/releases/download/v1.0.1/dravion-v1.0.1.zip';
        Http::fake([
            'api.github.com/*' => Http::response([[
                'tag_name'    => 'v1.0.1',
                'body'        => 'Some changes',
                'zipball_url' => 'https://api.github.com/repos/o/r/zipball/v1.0.1',
                'draft'       => false,
                'prerelease'  => false,
                'assets'      => [[
                    'name'                  => 'dravion-v1.0.1.zip',
                    'browser_download_url'  => $assetUrl,
                ]],
            ]], 200),
        ]);

        $releases = $this->svc()->getReleases();

        $this->assertSame($assetUrl, $releases[0]['zip_url'],
            'zip_url must point to the release asset (has public/build), not zipball_url (missing public/build)');
    }

    public function test_falls_back_to_zipball_when_no_release_asset(): void
    {
        // Older releases or releases without the custom ZIP should still work via zipball_url.
        config(['dravion.version' => '1.0.0', 'updater.owner' => 'o', 'updater.repo' => 'r', 'updater.token' => '']);

        $zipballUrl = 'https://api.github.com/repos/o/r/zipball/v1.0.1';
        Http::fake([
            'api.github.com/*' => Http::response([[
                'tag_name'    => 'v1.0.1',
                'body'        => 'Some changes',
                'zipball_url' => $zipballUrl,
                'draft'       => false,
                'prerelease'  => false,
                'assets'      => [],
            ]], 200),
        ]);

        $releases = $this->svc()->getReleases();

        $this->assertSame($zipballUrl, $releases[0]['zip_url'],
            'When no release asset exists, must fall back to zipball_url');
    }

    public function test_ignores_non_dravion_assets(): void
    {
        // Only assets named "dravion-*.zip" should be used; other attachments are ignored.
        config(['dravion.version' => '1.0.0', 'updater.owner' => 'o', 'updater.repo' => 'r', 'updater.token' => '']);

        $zipballUrl = 'https://api.github.com/repos/o/r/zipball/v1.0.1';
        Http::fake([
            'api.github.com/*' => Http::response([[
                'tag_name'    => 'v1.0.1',
                'body'        => 'Some changes',
                'zipball_url' => $zipballUrl,
                'draft'       => false,
                'prerelease'  => false,
                'assets'      => [[
                    'name'                 => 'checksums.txt',
                    'browser_download_url' => 'https://github.com/o/r/releases/download/v1.0.1/checksums.txt',
                ]],
            ]], 200),
        ]);

        $releases = $this->svc()->getReleases();

        $this->assertSame($zipballUrl, $releases[0]['zip_url'],
            'Non-dravion assets (e.g. checksums.txt) must be ignored; fall back to zipball_url');
    }
}
