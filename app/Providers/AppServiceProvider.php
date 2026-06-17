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
        $appUrl = getenv('APP_URL') ?: config('app.url');
        if (!$appUrl || $appUrl === 'http://localhost') {
            return;
        }

        $correctUrl = rtrim($appUrl, '/') . '/storage';

        // Override the config value so any NEW disk instances use the right URL.
        config(['filesystems.disks.public.url' => $correctUrl]);

        // Purge the cached disk adapter — FilesystemManager caches the adapter on first
        // use with the URL baked in. Without purge, config() change above has no effect.
        try {
            Storage::forgetDisk('public');
        } catch (\Throwable) {}
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
