<?php

namespace App\Support;

final class DomainHelper
{
    private const DEV_HOSTS = ['localhost', '127.0.0.1', '::1'];

    private const DEV_TLDS = ['.local', '.test', '.dev'];

    public static function isDevDomain(string $domain): bool
    {
        if (in_array($domain, self::DEV_HOSTS, true)) {
            return true;
        }

        foreach (self::DEV_TLDS as $tld) {
            if (str_ends_with($domain, $tld)) {
                return true;
            }
        }

        return false;
    }

    public static function fromAppUrl(): string
    {
        return parse_url((string) config('app.url'), PHP_URL_HOST) ?? '';
    }

    /** Masks a license key for display: "DRV-XXXXX" → "DRV-****" */
    public static function maskKey(string $key): string
    {
        $parts = explode('-', $key, 2);
        return ($parts[0] ?? 'DRV') . '-****';
    }
}
