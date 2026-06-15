<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Services\LangKeyExtractor;
use Illuminate\Console\Command;

class LangSeed extends Command
{
    protected $signature   = 'lang:seed';
    protected $description = 'Create EN + BG languages in DB and populate all translations from lang/ files.';

    public function handle(): int
    {
        $locales = [
            'en' => ['name' => 'English',   'flag' => '🇬🇧', 'default' => true],
            'bg' => ['name' => 'Bulgarian', 'flag' => '🇧🇬', 'default' => false],
        ];

        foreach ($locales as $code => $meta) {
            $lang = Language::firstOrCreate(
                ['code' => $code],
                ['name' => $meta['name'], 'flag' => $meta['flag'], 'is_default' => $meta['default']],
            );

            $keyValues = LangKeyExtractor::keyValues($code);

            // Fall back to EN keys with empty values if no native file
            if (empty($keyValues)) {
                $keyValues = array_fill_keys(LangKeyExtractor::keys('en'), '');
            }

            foreach ($keyValues as $key => $value) {
                $lang->lines()->updateOrCreate(['key' => $key], ['value' => $value]);
            }

            $this->info("✓ {$code}: " . count($keyValues) . ' keys seeded.');
        }

        return self::SUCCESS;
    }
}
