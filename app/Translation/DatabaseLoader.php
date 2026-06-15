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
            // DB not available (e.g. during install)
            return [];
        }
    }
}
