<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LicenseCheck
{
    private const TTL = 86400; // 24 hours

    public function handle(Request $request, Closure $next): Response
    {
        $licenseKey = config('dravion.license_key');

        // No license key → warn but don't block
        if (empty($licenseKey)) {
            session()->flash('license_warning', 'No license key configured.');
            return $next($request);
        }

        // Dev key: valid on dev domains only
        if (str_starts_with($licenseKey, 'DEV-')) {
            if (! $this->isDevDomain(parse_url(config('app.url'), PHP_URL_HOST) ?? '')) {
                session()->flash('license_warning', 'You are using a development license key. Please re-activate with your purchase code.');
            }
            return $next($request);
        }

        // Read the HMAC-signed cache via LicenseService
        $cache = LicenseService::readCachePublic();

        // Revalidate if cache is stale or missing
        if ($cache === null || (time() - ($cache['checked_at'] ?? 0)) > self::TTL) {
            $cache = $this->pingServer($licenseKey);
            LicenseService::writeCache($cache);
        }

        if (! ($cache['valid'] ?? false)) {
            session()->flash('license_warning', $cache['message'] ?? 'License invalid. Contact support.');
        }

        return $next($request);
    }

    private function pingServer(string $licenseKey): array
    {
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? request()->getHost();
        $url    = rtrim(config('dravion.license_server', 'https://apsbg.com/dravion-server'), '/')
                  . '/api/router.php?endpoint=validate';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode(['license_key' => $licenseKey, 'domain' => $domain]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Server unreachable → assume valid, retry next cycle
        if ($raw === false || $code === 0) {
            return ['valid' => true, 'checked_at' => time(), 'message' => null];
        }

        $data = json_decode($raw, true) ?? [];
        return [
            'valid'      => (bool) ($data['valid'] ?? false),
            'checked_at' => time(),
            'message'    => $data['message'] ?? null,
        ];
    }

    private function isDevDomain(string $domain): bool
    {
        return in_array($domain, ['localhost', '127.0.0.1'], true)
            || str_ends_with($domain, '.local')
            || str_ends_with($domain, '.test')
            || str_ends_with($domain, '.dev');
    }
}
