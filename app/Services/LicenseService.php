<?php

namespace App\Services;

use App\Contracts\LicenseServiceInterface;
use Illuminate\Support\Facades\Http;

class LicenseService implements LicenseServiceInterface
{
    private const CACHE_FILE = 'license.cache';

    public function activate(string $purchaseCode, string $domain): array
    {
        $server = rtrim(config('dravion.license_server', 'https://apsbg.com/dravion-server'), '/');

        try {
            $response = Http::timeout(10)->post("{$server}/api/router.php?endpoint=activate", [
                'purchase_code' => $purchaseCode,
                'domain'        => $domain,
            ]);

            $data = $response->json();

            if (! $response->successful()) {
                return ['error' => $data['error'] ?? $data['message'] ?? 'Activation failed.'];
            }

            if (! isset($data['license_key'])) {
                return ['error' => 'Invalid response from license server.'];
            }

            return $data;
        } catch (\Throwable $e) {
            return ['error' => 'Could not reach license server. Try again later.'];
        }
    }

    public function isValid(): bool
    {
        $key = (string) config('dravion.license_key', '');

        if ($key === '') {
            return false;
        }

        if (str_starts_with($key, 'DEV-')) {
            $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?? '';
            return $this->isDevDomain($host);
        }

        $cache = $this->readCache();
        if ($cache === null) {
            return false;
        }

        return (bool) ($cache['valid'] ?? false);
    }

    public function readCachePublic(): ?array
    {
        return $this->readCache();
    }

    public function writeCache(array $data): void
    {
        $payload   = json_encode($data);
        $signature = hash_hmac('sha256', $payload, $this->appKey());
        file_put_contents(
            storage_path(self::CACHE_FILE),
            json_encode(['payload' => $payload, 'sig' => $signature])
        );
    }

    private function readCache(): ?array
    {
        $path = storage_path(self::CACHE_FILE);
        if (! file_exists($path)) {
            return null;
        }

        $outer = json_decode((string) file_get_contents($path), true);
        if (! is_array($outer) || ! isset($outer['payload'], $outer['sig'])) {
            return null;
        }

        $expected = hash_hmac('sha256', $outer['payload'], $this->appKey());
        if (! hash_equals($expected, $outer['sig'])) {
            return null;
        }

        $data = json_decode($outer['payload'], true);
        return is_array($data) ? $data : null;
    }

    private function appKey(): string
    {
        return hash('sha256', config('app.key', 'fallback'));
    }

    private function isDevDomain(string $domain): bool
    {
        return in_array($domain, ['localhost', '127.0.0.1'], true)
            || str_ends_with($domain, '.local')
            || str_ends_with($domain, '.test')
            || str_ends_with($domain, '.dev');
    }
}
