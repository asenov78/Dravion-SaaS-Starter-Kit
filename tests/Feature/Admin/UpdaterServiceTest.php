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
        Http::fake([
            'api.github.com/*' => Http::response([
                'tag_name'    => $tag,
                'body'        => "## What's new\n- Stuff",
                'zipball_url' => 'https://api.github.com/repos/o/r/zipball/' . $tag,
            ], 200),
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
