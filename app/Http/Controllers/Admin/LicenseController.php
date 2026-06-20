<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\LicenseServiceInterface;
use App\Http\Controllers\Controller;
use App\Facades\ActivityLogger;
use App\Services\EnvWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function __construct(private LicenseServiceInterface $license) {}

    public function show(): RedirectResponse
    {
        return redirect()->route('admin.updates');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate(['license_key' => 'required|string|min:6']);

        $backTo = $this->resolveRedirect($request);
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
        $result = $this->license->activate($request->license_key, $domain);

        if (isset($result['error'])) {
            return redirect()->to($backTo)->withErrors(['license_key' => $result['error']]);
        }

        $licenseKey = $result['license_key'];
        if (! app()->environment('testing')) {
            $this->writeEnvKey('DRAVION_LICENSE_KEY', $licenseKey);
        }
        config(['dravion.license_key' => $licenseKey]);
        @unlink(storage_path('license.cache'));
        session()->forget('license_warning');

        ActivityLogger::log(
            'license', 'activated',
            'License activated for domain ' . ($domain),
            null, null,
            'activity.license_activated', ['domain' => $domain]
        );

        return redirect()->to($backTo)->with('success', __('flash.license_activated'));
    }

    public function remove(Request $request): RedirectResponse
    {
        $backTo = $this->resolveRedirect($request);
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';

        if (! app()->environment('testing')) {
            $this->writeEnvKey('DRAVION_LICENSE_KEY', '');
        }
        config(['dravion.license_key' => '']);
        @unlink(storage_path('license.cache'));

        ActivityLogger::log(
            'license', 'removed',
            'License removed for domain ' . $domain,
            null, null,
            'activity.license_removed', ['domain' => $domain]
        );

        return redirect()->to($backTo)->with('success', __('flash.license_removed'));
    }

    /**
     * Determine where to redirect after a license action.
     * Forms can pass a `_back` hidden field to override the default.
     * Allowed values: admin.updates or admin.license routes only (whitelist).
     */
    private function resolveRedirect(Request $request): string
    {
        $allowed = [route('admin.license'), route('admin.updates')];
        $back    = $request->input('_back');
        return (in_array($back, $allowed, true)) ? $back : route('admin.license');
    }

    private function writeEnvKey(string $key, string $value): void
    {
        EnvWriter::set(base_path('.env'), $key, $value);
    }
}

