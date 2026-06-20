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

    /** Flush the in-memory cache (useful in tests between assertions). */
    public static function flushCache(): void
    {
        static::$cache = [];
    }
}
