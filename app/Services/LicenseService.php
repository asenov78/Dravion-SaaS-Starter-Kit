<?php

namespace App\Services;

class LicenseService
{
    private const CACHE_FILE = 'license.cache';

    /**
     * Whether the installation holds a valid license.
     */
    public static function isValid(): bool
    {
        $key = (string) config('dravion.license_key', '');

        if ($key === '') {
            return false;
        }

        // Development keys: valid only on dev domains.
        if (str_starts_with($key, 'DEV-')) {
            $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?? '';
            return self::isDevDomain($host);
        }

        // Production key: require a verified, signed cache entry.
        // No cache → false (pessimistic). LicenseCheck middleware will ping
        // the server and write the cache on the next page request.
        $cache = self::readCache();
        if ($cache === null) {
            return false;
        }

        return (bool) ($cache['valid'] ?? false);
    }

    /**
     * Read the signed cache (callable by middleware without duplicating HMAC logic).
     */
    public static function readCachePublic(): ?array
    {
        return self::readCache();
    }

    /**
     * Write a signed cache entry (called by LicenseCheck middleware).
     */
    public static function writeCache(array $data): void
    {
        $payload   = json_encode($data);
        $signature = hash_hmac('sha256', $payload, self::appKey());
        file_put_contents(
            storage_path(self::CACHE_FILE),
            json_encode(['payload' => $payload, 'sig' => $signature])
        );
    }

    private static function readCache(): ?array
    {
        $path = storage_path(self::CACHE_FILE);
        if (! file_exists($path)) {
            return null;
        }

        $outer = json_decode((string) file_get_contents($path), true);
        if (! is_array($outer) || ! isset($outer['payload'], $outer['sig'])) {
            return null;
        }

        // Verify HMAC — tampered files are rejected.
        $expected = hash_hmac('sha256', $outer['payload'], self::appKey());
        if (! hash_equals($expected, $outer['sig'])) {
            return null;
        }

        $data = json_decode($outer['payload'], true);
        return is_array($data) ? $data : null;
    }

    private static function appKey(): string
    {
        // Derive a sub-key from APP_KEY so the signature is installation-specific.
        return hash('sha256', config('app.key', 'fallback'));
    }

    private static function isDevDomain(string $domain): bool
    {
        return in_array($domain, ['localhost', '127.0.0.1'], true)
            || str_ends_with($domain, '.local')
            || str_ends_with($domain, '.test')
            || str_ends_with($domain, '.dev');
    }
}
