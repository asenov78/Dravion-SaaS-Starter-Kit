<?php

namespace App\Services;

class LicenseService
{
    /**
     * Whether the installation holds a valid license.
     * Mirrors the gating logic in App\Http\Middleware\LicenseCheck.
     */
    public static function isValid(): bool
    {
        $key = (string) config('dravion.license_key', '');

        if ($key === '') {
            return false;
        }

        // Development keys are valid only on dev domains.
        if (str_starts_with($key, 'DEV-')) {
            $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?? '';
            return self::isDevDomain($host);
        }

        // Production key: honour the cached validation result if present.
        $cache = self::readCache();
        if ($cache !== null) {
            return (bool) ($cache['valid'] ?? false);
        }

        // No cache yet → optimistic, middleware will revalidate.
        return true;
    }

    private static function isDevDomain(string $domain): bool
    {
        return in_array($domain, ['localhost', '127.0.0.1'], true)
            || str_ends_with($domain, '.local')
            || str_ends_with($domain, '.test')
            || str_ends_with($domain, '.dev');
    }

    private static function readCache(): ?array
    {
        $path = storage_path('license.cache');
        if (! file_exists($path)) {
            return null;
        }
        $data = json_decode((string) file_get_contents($path), true);
        return is_array($data) ? $data : null;
    }
}
