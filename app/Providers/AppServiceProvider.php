<?php

namespace App\Providers;

use App\Contracts\LicenseServiceInterface;
use App\Models\User;
use App\Observers\UserObserver;
use App\Services\LicenseService;
use App\Translation\DatabaseLoader;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->extend('translation.loader', function ($original, $app) {
            return new DatabaseLoader($app['files'], $app['path.lang']);
        });

        $this->app->bind(LicenseServiceInterface::class, LicenseService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);

        Schema::defaultStringLength(191);

        $appUrl = config('app.url');
        if ($appUrl && $appUrl !== 'http://localhost') {
            URL::forceRootUrl($appUrl);
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        foreach (['framework/sessions', 'framework/cache/data', 'framework/views', 'logs'] as $dir) {
            $path = storage_path($dir);
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $this->ensureStorageSymlink();
        $this->fixStorageDiskUrl();
    }

    private function fixStorageDiskUrl(): void
    {
        $appUrl = $this->resolveAppUrl();
        if (!$appUrl || $appUrl === 'http://localhost') {
            return;
        }

        $correctUrl = rtrim($appUrl, '/') . '/storage';
        config(['filesystems.disks.public.url' => $correctUrl]);

        // Purge cached disk adapter so next Storage::url() call picks up the new URL.
        try {
            Storage::forgetDisk('public');
        } catch (\Throwable) {}
    }

    /**
     * Resolve the true APP_URL, bypassing Dotenv immutable mode.
     *
     * On shared hosting Apache often exports APP_URL as a system env var
     * without the subdirectory path (e.g. https://domain.com instead of
     * https://domain.com/dravion). Laravel's Dotenv::createImmutable() keeps
     * the system env var and ignores the .env file value, so both
     * config('app.url') and getenv() return the wrong host-only URL.
     *
     * Priority:
     * 1. Compute from the live HTTP request (SCRIPT_NAME) — always correct for web.
     * 2. Read the .env file directly, bypassing Dotenv and system env.
     * 3. Fall back to the Laravel config as a last resort.
     */
    private function resolveAppUrl(): string
    {
        // 1. Derive from current HTTP request — immune to env var misconfig.
        if (!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['SCRIPT_NAME'])) {
            $scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host      = $_SERVER['HTTP_HOST'];
            $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
            return $scheme . '://' . $host . $scriptDir;
        }

        // 2. Read .env file directly (e.g. for artisan commands).
        $envFile = base_path('.env');
        if (file_exists($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (preg_match('/^APP_URL=(.+)$/', $line, $m)) {
                    return trim($m[1], " \t\n\r\"'");
                }
            }
        }

        // 3. Laravel config — may be wrong if system env overrides .env.
        return (string) config('app.url', '');
    }

    private function ensureStorageSymlink(): void
    {
        $link   = public_path('storage');
        $target = storage_path('app/public');

        // Remove broken symlink so .htaccess falls through to PHP route
        if (is_link($link) && !is_dir($link)) {
            @unlink($link);
        }

        // Try to create symlink if still missing
        if (!file_exists($link) && !is_link($link)) {
            try {
                symlink($target, $link);
            } catch (\Throwable) {
                // Try relative path (works on more shared hosts)
                try {
                    $relative = implode('/', array_fill(0, substr_count(str_replace(base_path(), '', public_path()), DIRECTORY_SEPARATOR), '..'))
                        . '/storage/app/public';
                    @symlink($relative, $link);
                } catch (\Throwable) {}
            }
        }
    }
}
