<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UpdateWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('github_latest_version');
    }

    private function signature(string $body, string $secret): string
    {
        return 'sha256=' . hash_hmac('sha256', $body, $secret);
    }

    private function payload(string $version = '2.0.0', string $action = 'published'): array
    {
        return ['action' => $action, 'release' => ['tag_name' => "v{$version}"]];
    }

    public function test_valid_release_event_caches_version(): void
    {
        config(['updater.webhook_secret' => '']);

        $this->postJson(
            route('webhook.github.releases'),
            $this->payload('2.5.0'),
            ['X-GitHub-Event' => 'release']
        )->assertStatus(200);

        $this->assertEquals('2.5.0', Cache::get('github_latest_version'));
    }

    public function test_tag_with_v_prefix_is_stripped(): void
    {
        config(['updater.webhook_secret' => '']);

        $this->postJson(
            route('webhook.github.releases'),
            $this->payload('1.2.3'),
            ['X-GitHub-Event' => 'release']
        )->assertStatus(200);

        $this->assertEquals('1.2.3', Cache::get('github_latest_version'));
    }

    public function test_valid_hmac_signature_accepted(): void
    {
        config(['updater.webhook_secret' => 'mysecret']);
        $body = json_encode($this->payload('3.0.0'));

        $this->call('POST', route('webhook.github.releases'), [], [], [], [
            'HTTP_X-GitHub-Event'        => 'release',
            'HTTP_X-Hub-Signature-256'   => $this->signature($body, 'mysecret'),
            'CONTENT_TYPE'               => 'application/json',
        ], $body)->assertStatus(200);

        $this->assertEquals('3.0.0', Cache::get('github_latest_version'));
    }

    public function test_invalid_signature_rejected_with_401(): void
    {
        config(['updater.webhook_secret' => 'mysecret']);

        $this->call('POST', route('webhook.github.releases'), [], [], [], [
            'HTTP_X-GitHub-Event'        => 'release',
            'HTTP_X-Hub-Signature-256'   => 'sha256=invalidsignature',
            'CONTENT_TYPE'               => 'application/json',
        ], json_encode($this->payload()))->assertStatus(401);

        $this->assertNull(Cache::get('github_latest_version'));
    }

    public function test_missing_signature_when_secret_set_rejected_with_401(): void
    {
        config(['updater.webhook_secret' => 'mysecret']);

        $this->postJson(
            route('webhook.github.releases'),
            $this->payload(),
            ['X-GitHub-Event' => 'release']
        )->assertStatus(401);

        $this->assertNull(Cache::get('github_latest_version'));
    }

    public function test_non_published_action_does_not_cache_version(): void
    {
        config(['updater.webhook_secret' => '']);

        $this->postJson(
            route('webhook.github.releases'),
            $this->payload('5.0.0', 'created'),
            ['X-GitHub-Event' => 'release']
        )->assertStatus(200);

        $this->assertNull(Cache::get('github_latest_version'));
    }

    public function test_non_release_event_does_not_cache_version(): void
    {
        config(['updater.webhook_secret' => '']);

        $this->postJson(
            route('webhook.github.releases'),
            ['action' => 'opened'],
            ['X-GitHub-Event' => 'pull_request']
        )->assertStatus(200);

        $this->assertNull(Cache::get('github_latest_version'));
    }

    public function test_no_secret_configured_accepts_unsigned_request(): void
    {
        config(['updater.webhook_secret' => '']);

        $this->postJson(
            route('webhook.github.releases'),
            $this->payload('4.0.0'),
            ['X-GitHub-Event' => 'release']
        )->assertStatus(200);

        $this->assertEquals('4.0.0', Cache::get('github_latest_version'));
    }
}
