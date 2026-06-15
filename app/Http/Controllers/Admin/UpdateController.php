<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LicenseService;
use App\Services\UpdaterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UpdateController extends Controller
{
    public function index(UpdaterService $updater): View
    {
        $licensed = LicenseService::isValid();

        // Never call the update server without a valid license.
        $update = $licensed ? $updater->checkForUpdate() : null;

        return view('admin.updates.index', [
            'licensed' => $licensed,
            'current'  => $updater->getCurrentVersion(),
            'update'   => $update,
        ]);
    }

    public function check(UpdaterService $updater): JsonResponse
    {
        if (! LicenseService::isValid()) {
            return response()->json(['error' => __('updates.license_required')], 403);
        }

        return response()->json($updater->checkForUpdate());
    }

    public function install(Request $request, UpdaterService $updater): JsonResponse
    {
        if (! LicenseService::isValid()) {
            abort(403, __('updates.license_required'));
        }

        $data = $request->validate(['zip_url' => 'required|url']);

        $result = $updater->downloadAndInstall($data['zip_url']);

        return response()->json($result, $result['ok'] ? 200 : 500);
    }
}
