<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\LicenseServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UpdateInstalledNotification;
use App\Rules\GitHubZipUrl;
use App\Services\UpdaterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UpdateController extends Controller
{
    public function __construct(private LicenseServiceInterface $license) {}

    public function index(UpdaterService $updater): View
    {
        $licensed = $this->license->isValid();

        // Always check for the latest version so the user can SEE a new release
        // exists — but downloading/installing is gated by a valid license.
        $update = $updater->checkForUpdate();
        if (! $licensed) {
            $update['zip_url'] = null;
            $update['newer']   = array_map(function ($r) {
                $r['zip_url'] = null;
                return $r;
            }, $update['newer']);
        }

        $updater->ensureHistoryExists();

        return view('admin.updates.index', [
            'licensed' => $licensed,
            'current'  => $updater->getCurrentVersion(),
            'update'   => $update,
            'history'  => $updater->getUpdateHistory(),
        ]);
    }

    public function check(UpdaterService $updater): JsonResponse
    {
        $update = $updater->checkForUpdate();

        // Hide download URLs from unlicensed installs (both top-level and per-release).
        if (! $this->license->isValid()) {
            $update['zip_url'] = null;
            $update['newer']   = array_map(function ($r) {
                $r['zip_url'] = null;
                return $r;
            }, $update['newer']);
        }

        return response()->json($update);
    }

    public function install(Request $request, UpdaterService $updater): JsonResponse
    {
        if (! $this->license->isValid()) {
            abort(403, __('updates.license_required'));
        }

        $data = $request->validate([
            'zip_url' => ['required', 'url', new GitHubZipUrl],
        ]);

        $result = $updater->downloadAndInstall($data['zip_url']);

        if ($result['ok'] ?? false) {
            try {
                $version = $result['version'] ?? 'unknown';
                User::role('admin')->get()->each->notify(new UpdateInstalledNotification($version));
            } catch (\Throwable) {}
        }

        return response()->json($result, $result['ok'] ? 200 : 500);
    }
}
