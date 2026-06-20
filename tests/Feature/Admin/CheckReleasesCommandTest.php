<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckReleasesCommandTest extends TestCase
{
    use RefreshDatabase;

    private function fakeRelease(string $tag): void
    {
        Http::fake([
            'api.github.com/*' => Http::response([[
                'tag_name'    => $tag,
                'body'        => 'Release notes',
                'zipball_url' => 'https://api.github.com/repos/o/r/zipball/' . $tag,
                'draft'       => false,
                'prerelease'  => false,
            ]], 200),
        ]);
        config(['updater.owner' => 'o', 'updater.repo' => 'r']);
    }

    public function test_command_caches_latest_version(): void
    {
        $this->fakeRelease('v9.9.9');
        Cache::forget('github_latest_version');

        $this->artisan('updates:check-releases')
            ->assertSuccessful();

        $this->assertSame('9.9.9', Cache::get('github_latest_version'));
    }

    public function test_command_outputs_latest_version(): void
    {
        $this->fakeRelease('v2.5.0');

        $this->artisan('updates:check-releases')
            ->expectsOutputToContain('v2.5.0')
            ->assertSuccessful();
    }

    public function test_command_returns_failure_when_github_unreachable(): void
    {
        Http::fake(['api.github.com/*' => Http::response(null, 503)]);
        config(['updater.owner' => 'o', 'updater.repo' => 'r']);

        $this->artisan('updates:check-releases')
            ->assertFailed();
    }

    public function test_command_returns_failure_when_owner_not_configured(): void
    {
        config(['updater.owner' => '', 'updater.repo' => '']);

        $this->artisan('updates:check-releases')
            ->assertFailed();
    }

    public function test_command_strips_v_prefix_from_cached_version(): void
    {
        $this->fakeRelease('v1.2.3');

        $this->artisan('updates:check-releases')->assertSuccessful();

        // Stored without 'v' prefix so MenuHelper version_compare works
        $this->assertSame('1.2.3', Cache::get('github_latest_version'));
    }
}
