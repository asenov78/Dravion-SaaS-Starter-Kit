<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Request-scoped in-memory cache — avoids repeated DB hits per request. */
    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (! array_key_exists($key, static::$cache)) {
            static::$cache[$key] = static::where('key', $key)->value('value');
        }

        return static::$cache[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$cache[$key] = $value;
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::set($key, $value);
        }
    }

    /**
     * Get a locale-aware setting value.
     * Tries "{key}_{locale}" first; falls back to "{key}" (the EN/default value).
     */
    public static function getLocalized(string $key, mixed $default = ''): mixed
    {
        $locale = app()->getLocale();

        if ($locale !== 'en') {
            $localized = static::get("{$key}_{$locale}", '');
            if ($localized !== '') {
                return $localized;
            }
        }

        return static::get($key, $default);
    }

    /** Flush the in-memory cache (useful in tests between assertions). */
    public static function flushCache(): void
    {
        static::$cache = [];
    }
}
