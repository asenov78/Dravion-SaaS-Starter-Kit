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
use Illuminate\Http\Request;
use Illuminate\View\View;

class UpdateController extends Controller
{
    public function __construct(private LicenseServiceInterface $license) {}

    public function index(UpdaterService $updater): View
    {
        // Page display uses cached status (fast) — install/check do live verification.
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
     * Install an update — live license verification immediately before download
     * to prevent installs on suspended/revoked licenses.
     */
    public function install(Request $request, UpdaterService $updater): JsonResponse
    {
        // Live check: re-verify right now, not from 24h-old cache.
        if (! $this->license->isValidLive()) {
            abort(403, __('updates.license_required'));
        }

        $data = $request->validate([
            'zip_url'   => ['required', 'url', new GitHubZipUrl],
            'changelog' => ['nullable', 'string', 'max:10000'],
        ]);

        $result = $updater->downloadAndInstall($data['zip_url'], $data['changelog'] ?? '');

        if ($result['ok'] ?? false) {
            try {
                $version = $result['version'] ?? 'unknown';
                User::role('admin')->get()->each->notify(new UpdateInstalledNotification($version));
            } catch (\Throwable) {}
        }

        return response()->json($result, $result['ok'] ? 200 : 500);
    }
}
