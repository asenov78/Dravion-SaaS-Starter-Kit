<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\LicenseServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UpdateInstalledNotification;
use App\Rules\GitHubZipUrl;
use App\Services\UpdaterService;
use App\Support\DomainHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class UpdateController extends Controller
{
    public function __construct(private LicenseServiceInterface $license) {}

    public function index(UpdaterService $updater): View
    {
        // Cache-only check for page display (fast). install() + check() do live verification.
        $licensed = $this->license->isValid();

        $update = $updater->checkForUpdate();
        if (! $licensed) {
            $update['zip_url'] = null;
            $update['newer']   = array_map(function ($r) {
                $r['zip_url'] = null;
                return $r;
            }, $update['newer']);
        }

        $updater->ensureHistoryExists();

        $raw    = config('dravion.license_key', '');
        $masked = $raw ? DomainHelper::maskKey($raw) : null;
        $valid  = $masked && ! session('license_warning');

        // Keep sidebar badge in sync with the version we just fetched.
        if ($update['latest'] !== null) {
            Cache::put('github_latest_version', $update['latest'], now()->addHours(6));
        }

        return view('admin.updates.index', [
            'licensed' => $licensed,
            'current'  => $updater->getCurrentVersion(),
            'update'   => $update,
            'history'  => $updater->getUpdateHistory(),
            'masked'   => $masked,
            'valid'    => $valid,
        ]);
    }

    /**
     * Clear license cache and do a live ping to see if the key is now valid.
     * Redirects back to updates page with success or warning flash.
     */
    public function checkLicense(): RedirectResponse
    {
        @unlink(storage_path('license.cache'));

        if ($this->license->isValidLive()) {
            return redirect()->route('admin.updates')
                ->with('success', __('flash.license_verified'));
        }

        return redirect()->route('admin.updates')
            ->with('license_warning', __('flash.license_not_found'));
    }

    /**
     * "Check again" — clears license cache + github_latest_version cache so
     * LicenseCheck middleware does a live ping on the subsequent redirect and
     * index() re-fetches GitHub releases fresh.
     */
    public function checkAll(): RedirectResponse
    {
        @unlink(storage_path('license.cache'));
        Cache::forget('github_latest_version');

        return redirect()->route('admin.updates')
            ->with('success', __('flash.update_check_done'));
    }

    /**
     * AJAX check for new version — live license verification to catch
     * suspended/revoked keys before revealing download URLs.
     */
    public function check(UpdaterService $updater): JsonResponse
    {
        // Live check: catches suspend/revoke that happened after the last cache refresh.
        if (! $this->license->isValidLive()) {
            $update = $updater->checkForUpdate();
            $update['zip_url'] = null;
            $update['newer']   = array_map(function ($r) {
                $r['zip_url'] = null;
                return $r;
            }, $update['newer'] ?? []);
            return response()->json($update);
        }

        return response()->json($updater->checkForUpdate());
    }

    /**
     * Install a single update version.
     *
     * RULE: the frontend calls this endpoint once per version, sequentially
     * (oldest version first). Each call blocks until the install completes.
     * NEVER batch multiple versions in one call — migrations must run in order
     * and each version's post-install state is the base for the next version.
     *
     * Live license verification runs on every call to catch suspensions that
     * occurred after the initial page load cache was written.
     */
    public function install(Request $request, UpdaterService $updater): JsonResponse
    {
        // Live check: re-verify right now, not from 24h-old cache.
        if (! $this->license->isValidLive()) {
            return response()->json(['ok' => false, 'message' => __('updates.license_required')], 403);
        }

        $data = $request->validate([
            'zip_url'   => ['required', 'string', 'url', new GitHubZipUrl],
            'changelog' => ['nullable', 'string', 'max:10000'],
        ]);

        $lock = cache()->lock('dravion-update-install', 120);
        if (! $lock->get()) {
            return response()->json(['ok' => false, 'message' => __('updates.install_in_progress')], 409);
        }

        try {
            $result = $updater->downloadAndInstall($data['zip_url'], $data['changelog'] ?? '');
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        } finally {
            $lock->release();
        }

        if ($result['ok'] ?? false) {
            try {
                $version = $result['version'] ?? 'unknown';
                User::role('admin')->get()->each->notify(new UpdateInstalledNotification($version));
            } catch (\Throwable) {}
        }

        return response()->json($result, ($result['ok'] ?? false) ? 200 : 500);
    }
}
