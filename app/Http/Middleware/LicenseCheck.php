<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LicenseCheck
{
    private const CACHE_FILE = 'license.cache';
    private const TTL        = 86400; // 24 hours

    public function handle(Request $request, Closure $next): Response
    {
        $licenseKey = config('dravion.license_key');

        // No license key written → warn but don't block
        if (empty($licenseKey)) {
            session()->flash('license_warning', 'No license key configured.');
            return $next($request);
        }

        // Dev key: valid on dev domains, prompt re-activation on production
        if (str_starts_with($licenseKey, 'DEV-')) {
            if (! $this->isDevDomain(parse_url(config('app.url'), PHP_URL_HOST) ?? '')) {
                session()->flash('license_warning', 'You are using a development license key. Please re-activate with your purchase code.');
            }
            return $next($request);
        }

        $cache = $this->readCache();

        // Revalidate if cache is stale or missing
        if ($cache === null || (time() - ($cache['checked_at'] ?? 0)) > self::TTL) {
            $cache = $this->pingServer($licenseKey);
            $this->writeCache($cache);
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

    private function readCache(): ?array
    {
        $path = storage_path(self::CACHE_FILE);
        if (! file_exists($path)) return null;
        $data = json_decode(file_get_contents($path), true);
        return is_array($data) ? $data : null;
    }

    private function writeCache(array $data): void
    {
        file_put_contents(storage_path(self::CACHE_FILE), json_encode($data));
    }
}
