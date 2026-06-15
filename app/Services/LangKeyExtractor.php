<?php

namespace App\Services;

use Illuminate\Support\Arr;

class LangKeyExtractor
{
    public static function keys(string $locale = 'en'): array
    {
        return array_keys(static::keyValues($locale));
    }

    public static function keyValues(string $locale = 'en'): array
    {
        $langPath = base_path("lang/{$locale}");

        if (! is_dir($langPath)) {
            return [];
        }

        $result = [];

        foreach (glob("{$langPath}/*.php") as $file) {
            $group = basename($file, '.php');
            $lines = require $file;

            if (! is_array($lines)) {
                continue;
            }

            foreach (Arr::dot($lines) as $key => $value) {
                $result["{$group}.{$key}"] = (string) $value;
            }
        }

        return $result;
    }
}
