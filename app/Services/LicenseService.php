<?php

namespace App\Services;

use App\Contracts\LicenseServiceInterface;
use App\Support\DomainHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

            $data = $response->json() ?? [];

            if (! $response->successful()) {
                return ['error' => $data['error'] ?? $data['message'] ?? 'Activation failed.'];
            }

            if (! isset($data['license_key'])) {
                return ['error' => 'Invalid response from license server.'];
            }

            return $data;
        } catch (\Throwable) {
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
            return DomainHelper::isDevDomain(DomainHelper::fromAppUrl());
        }

        $cache = $this->readCache();
        if ($cache === null) {
            return false;
        }

        return (bool) ($cache['valid'] ?? false);
    }

    public function isValidLive(): bool
    {
        $key = (string) config('dravion.license_key', '');

        if ($key === '') {
            return false;
        }

        if (str_starts_with($key, 'DEV-')) {
            return DomainHelper::isDevDomain(DomainHelper::fromAppUrl());
        }

        $result = $this->verifyNow();
        return (bool) ($result['valid'] ?? false);
    }

    public function verifyNow(): array
    {
        $key = (string) config('dravion.license_key', '');

        if ($key === '') {
            return ['valid' => false, 'checked_at' => time(), 'message' => 'No license key configured.', 'status' => 'missing'];
        }

        if (str_starts_with($key, 'DEV-')) {
            $valid = DomainHelper::isDevDomain(DomainHelper::fromAppUrl());
            return ['valid' => $valid, 'checked_at' => time(), 'message' => null, 'status' => $valid ? 'active' : 'dev_key_on_prod'];
        }

        $domain = DomainHelper::fromAppUrl() ?: 'localhost';
        $server = rtrim(config('dravion.license_server', 'https://apsbg.com/dravion-server'), '/');

        try {
            $response = Http::timeout(8)->post("{$server}/api/router.php?endpoint=validate", [
                'license_key' => $key,
                'domain'      => $domain,
            ]);

            $data = $response->json() ?? [];
            $result = [
                'valid'      => (bool) ($data['valid'] ?? false),
                'checked_at' => time(),
                'message'    => $data['message'] ?? null,
                'status'     => $data['status'] ?? ($data['valid'] ? 'active' : 'invalid'),
            ];

            $this->writeCache($result);
            return $result;
        } catch (\Throwable) {
            // Server unreachable → fail-open using cached value
            Log::warning('License server unreachable — falling back to cached result');
            $cached = $this->readCache();
            $result = [
                'valid'      => (bool) ($cached['valid'] ?? false),
                'checked_at' => time(),
                'message'    => 'License server unreachable. Using cached result.',
                'status'     => 'server_unreachable',
            ];
            $this->writeCache($result);
            return $result;
        }
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
}
