<?php

namespace Tests\Feature\Admin;

use App\Services\UpdaterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdaterServiceTest extends TestCase
{
    use RefreshDatabase;

    private function fakeRelease(string $tag): void
    {
        $this->fakeReleases([$tag]);
    }

    /** @param string[] $tags newest first */
    private function fakeReleases(array $tags): void
    {
        Http::fake([
            'api.github.com/*' => Http::response(array_map(fn ($tag) => [
                'tag_name'    => $tag,
                'body'        => "## What's new in {$tag}\n- Stuff",
                'zipball_url' => 'https://api.github.com/repos/o/r/zipball/' . $tag,
                'draft'       => false,
                'prerelease'  => false,
            ], $tags), 200),
        ]);
        config(['updater.owner' => 'o', 'updater.repo' => 'r', 'updater.token' => '']);
    }

    public function test_get_current_version_reads_config(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->assertSame('1.2.29', (new UpdaterService)->getCurrentVersion());
    }

    public function test_check_detects_newer_version(): void
    {
        config(['dravion.version' => '1.2.29']);
        $this->fakeRelease('v1.3.0');

        $result = (new UpdaterService)->checkForUpdate();

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

        $result = (new UpdaterService)->checkForUpdate();

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

        $this->assertSame([], (new UpdaterService)->checkForUpdate()['newer']);
    }

    public function test_check_no_update_when_same_version(): void
    {
        config(['dravion.version' => '1.3.0']);
        $this->fakeRelease('v1.3.0');

        $result = (new UpdaterService)->checkForUpdate();

        $this->assertFalse($result['has_update']);
    }

    public function test_check_no_update_when_current_is_newer(): void
    {
        config(['dravion.version' => '1.4.0']);
        $this->fakeRelease('v1.3.0');

        $this->assertFalse((new UpdaterService)->checkForUpdate()['has_update']);
    }

    public function test_check_handles_github_error_gracefully(): void
    {
        config(['dravion.version' => '1.2.29']);
        Http::fake(['api.github.com/*' => Http::response('nope', 404)]);
        config(['updater.owner' => 'o', 'updater.repo' => 'r']);

        $result = (new UpdaterService)->checkForUpdate();

        $this->assertFalse($result['has_update']);
        $this->assertNull($result['latest']);
    }
}
