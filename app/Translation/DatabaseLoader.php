<?php

namespace App\Translation;

use App\Models\Language;
use Illuminate\Support\Arr;
use Illuminate\Translation\FileLoader;

class DatabaseLoader extends FileLoader
{
    public function load($locale, $group, $namespace = null): array
    {
        // 1. EN file — base fallback for all keys
        $base = parent::load('en', $group, $namespace);

        // 2. Locale file — file-based translations (lang/bg/*.php etc.)
        $file = $locale !== 'en' ? parent::load($locale, $group, $namespace) : [];

        // 3. DB lines — override file-based translations
        $db = $this->loadFromDb($locale, $group);

        return array_replace_recursive($base, $file, $db);
    }

    private function loadFromDb(string $locale, string $group): array
    {
        $cacheKey = "translations:{$locale}:{$group}";

        return cache()->remember($cacheKey, now()->addHours(24), function () use ($locale, $group) {
            try {
                $language = Language::where('code', $locale)->first();
                if (! $language) {
                    return [];
                }

                $prefix = $group . '.';
                $lines  = $language->lines()
                    ->where('key', 'like', $prefix . '%')
                    ->where('value', '!=', '')
                    ->pluck('value', 'key');

                $result = [];
                foreach ($lines as $key => $value) {
                    $shortKey = substr($key, strlen($prefix));
                    Arr::set($result, $shortKey, $value);
                }

                return $result;
            } catch (\Throwable) {
                return [];
            }
        });
    }

    public static function clearCache(?string $locale = null): void
    {
        if ($locale) {
            // Clear all groups for this locale — pattern-match keys
            foreach (['app', 'auth', 'nav', 'flash', 'ui', 'users', 'roles', 'settings', 'pages', 'install'] as $group) {
                cache()->forget("translations:{$locale}:{$group}");
            }
        } else {
            cache()->flush();
        }
    }
}
