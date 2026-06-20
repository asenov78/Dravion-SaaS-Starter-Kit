<?php

namespace App\Http\Middleware;

use App\Contracts\LicenseServiceInterface;
use App\Support\DomainHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LicenseCheck
{
    private const TTL = 86400;

    public function __construct(private LicenseServiceInterface $license) {}

    public function handle(Request $request, Closure $next): Response
    {
        $licenseKey = config('dravion.license_key');

        // No license key → warn but don't block
        if (empty($licenseKey)) {
            session()->flash('license_warning', 'No license key configured.');
            return $next($request);
        }

        // Dev key: valid on dev domains only (no server ping)
        if (str_starts_with($licenseKey, 'DEV-')) {
            if (! DomainHelper::isDevDomain(DomainHelper::fromAppUrl())) {
                session()->flash('license_warning', 'You are using a development license key. Please re-activate with your purchase code.');
            }
            return $next($request);
        }

        // Refresh cache if missing or stale (> 24 h)
        $cache = $this->license->readCachePublic();
        if ($cache === null || (time() - ($cache['checked_at'] ?? 0)) > self::TTL) {
            $cache = $this->license->verifyNow();
        }

        if (! ($cache['valid'] ?? false)) {
            session()->flash('license_warning', $cache['message'] ?? 'License invalid. Contact support.');
        }

        return $next($request);
    }
}
