<?php

namespace Tests\Unit;

use App\Services\UpdaterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdaterServiceSortTest extends TestCase
{
    use RefreshDatabase;

    private function svc(): UpdaterService
    {
        return app(UpdaterService::class);
    }

    public function test_releases_sorted_by_semver_descending_not_publish_order(): void
    {
        Http::fake([
            'api.github.com/*' => Http::response([
                // GitHub returns newest-published first — but v1.4.0 was published after v1.10.0
                ['tag_name' => 'v1.4.0', 'body' => '', 'zipball_url' => 'https://example.com/1.4.0.zip', 'draft' => false],
                ['tag_name' => 'v1.10.0', 'body' => '', 'zipball_url' => 'https://example.com/1.10.0.zip', 'draft' => false],
                ['tag_name' => 'v1.2.0', 'body' => '', 'zipball_url' => 'https://example.com/1.2.0.zip', 'draft' => false],
            ], 200),
        ]);

        $releases = $this->svc()->getReleases();

        $this->assertSame('1.10.0', $releases[0]['version'], 'Highest semver must be first');
        $this->assertSame('1.4.0',  $releases[1]['version']);
        $this->assertSame('1.2.0',  $releases[2]['version']);
    }

    public function test_get_latest_release_returns_highest_semver(): void
    {
        Http::fake([
            'api.github.com/*' => Http::response([
                ['tag_name' => 'v1.4.0',  'body' => '', 'zipball_url' => 'https://example.com/1.4.0.zip',  'draft' => false],
                ['tag_name' => 'v1.10.0', 'body' => '', 'zipball_url' => 'https://example.com/1.10.0.zip', 'draft' => false],
            ], 200),
        ]);

        $latest = $this->svc()->getLatestRelease();

        $this->assertSame('1.10.0', $latest['version']);
    }

    public function test_has_update_true_when_installed_older_than_github(): void
    {
        config(['dravion.version' => '1.4.0']);

        Http::fake([
            'api.github.com/*' => Http::response([
                ['tag_name' => 'v1.10.0', 'body' => '', 'zipball_url' => 'https://example.com/1.10.0.zip', 'draft' => false],
                ['tag_name' => 'v1.4.0',  'body' => '', 'zipball_url' => 'https://example.com/1.4.0.zip',  'draft' => false],
            ], 200),
        ]);

        $info = $this->svc()->checkForUpdate();

        $this->assertSame('1.4.0',  $info['current']);
        $this->assertSame('1.10.0', $info['latest']);
        $this->assertTrue($info['has_update'], 'Must show update available when installed < GitHub');
    }

    public function test_has_update_false_when_on_latest(): void
    {
        config(['dravion.version' => '1.10.0']);

        Http::fake([
            'api.github.com/*' => Http::response([
                ['tag_name' => 'v1.10.0', 'body' => '', 'zipball_url' => 'https://example.com/1.10.0.zip', 'draft' => false],
            ], 200),
        ]);

        $info = $this->svc()->checkForUpdate();

        $this->assertSame('1.10.0', $info['current']);
        $this->assertSame('1.10.0', $info['latest']);
        $this->assertFalse($info['has_update'], 'Must NOT show update when already on latest');
    }

    public function test_draft_releases_excluded(): void
    {
        Http::fake([
            'api.github.com/*' => Http::response([
                ['tag_name' => 'v2.0.0', 'body' => '', 'zipball_url' => 'https://example.com/2.0.0.zip', 'draft' => true],
                ['tag_name' => 'v1.5.0', 'body' => '', 'zipball_url' => 'https://example.com/1.5.0.zip', 'draft' => false],
            ], 200),
        ]);

        $latest = $this->svc()->getLatestRelease();

        $this->assertSame('1.5.0', $latest['version'], 'Draft releases must not appear');
    }
}
