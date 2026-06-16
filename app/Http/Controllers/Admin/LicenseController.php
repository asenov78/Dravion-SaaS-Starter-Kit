<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EnvWriter;
use App\Services\LicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LicenseController extends Controller
{
    public function show(): View
    {
        $raw = config('dravion.license_key', '');
        $masked = $raw ? $this->mask($raw) : null;
        $valid  = $masked && ! session('license_warning');

        return view('admin.license', compact('masked', 'valid'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate(['license_key' => 'required|string|min:6']);

        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
        $result = LicenseService::activate($request->license_key, $domain);

        if (isset($result['error'])) {
            return redirect()->route('admin.license')->withErrors(['license_key' => $result['error']]);
        }

        $licenseKey = $result['license_key'];
        if (! app()->environment('testing')) {
            $this->writeEnvKey('DRAVION_LICENSE_KEY', $licenseKey);
        }
        config(['dravion.license_key' => $licenseKey]);
        @unlink(storage_path('license.cache'));
        session()->forget('license_warning');

        return redirect()->route('admin.license')->with('success', __('flash.license_activated'));
    }

    public function remove(): RedirectResponse
    {
        if (! app()->environment('testing')) {
            $this->writeEnvKey('DRAVION_LICENSE_KEY', '');
        }
        config(['dravion.license_key' => '']);
        @unlink(storage_path('license.cache'));

        return redirect()->route('admin.license')->with('success', __('flash.license_removed'));
    }

    private function mask(string $key): string
    {
        $parts = explode('-', $key, 2);
        return ($parts[0] ?? 'DRV') . '-****';
    }

    private function writeEnvKey(string $key, string $value): void
    {
        EnvWriter::set(base_path('.env'), $key, $value);
    }
}
