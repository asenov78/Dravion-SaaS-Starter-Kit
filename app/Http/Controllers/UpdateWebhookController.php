<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class UpdateWebhookController extends Controller
{
    /**
     * Receive a GitHub "release" webhook and cache the new version so the
     * admin header badge appears without anyone clicking "Check Again".
     *
     * GitHub delivers this as POST with X-GitHub-Event: release and
     * action: published when a Release is published (not just tagged).
     *
     * SECURITY: If GITHUB_WEBHOOK_SECRET is set, the payload must carry a valid
     * X-Hub-Signature-256 header signed with that secret using HMAC-SHA256.
     */
    public function handle(Request $request): Response
    {
        $this->verifySignature($request);

        $event  = $request->header('X-GitHub-Event', '');
        $action = $request->input('action', '');

        if ($event === 'release' && $action === 'published') {
            $tag     = $request->input('release.tag_name', '');
            $version = ltrim($tag, 'vV');

            if ($version && preg_match('/^\d+\.\d+\.\d+/', $version)) {
                Cache::put('github_latest_version', $version, now()->addDays(7));
            }
        }

        return response('', 200);
    }

    private function verifySignature(Request $request): void
    {
        $secret = config('updater.webhook_secret', '');

        if (! $secret) {
            abort(401, 'Webhook secret not configured.');
        }

        $header = $request->header('X-Hub-Signature-256', '');

        if (! $header) {
            abort(401, 'Missing signature.');
        }

        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        if (! hash_equals($expected, $header)) {
            abort(401, 'Invalid signature.');
        }
    }
}
