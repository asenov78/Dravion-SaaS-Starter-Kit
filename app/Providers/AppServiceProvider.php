<?php

namespace App\Providers;

use App\Contracts\LicenseServiceInterface;
use App\Models\User;
use App\Observers\UserObserver;
use App\Services\LicenseService;
use App\Translation\DatabaseLoader;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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

        // Fix storage disk URL for shared hosting where Apache may export APP_URL
        // as a system env var without the subdirectory path (e.g. https://host
        // instead of https://host/dravion). The disk config bakes in env('APP_URL')
        // at parse time. We override it at runtime using url() which correctly derives
        // the base path from SCRIPT_NAME via Symfony HttpFoundation — same mechanism
        // that makes route() generate correct URLs.
        if (!app()->runningInConsole() && !empty($_SERVER['HTTP_HOST'])) {
            try {
                config(['filesystems.disks.public.url' => url('storage')]);
                Storage::forgetDisk('public');
            } catch (\Throwable) {}
        }

        foreach (['framework/sessions', 'framework/cache/data', 'framework/views', 'logs'] as $dir) {
            $path = storage_path($dir);
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $this->ensureStorageSymlink();
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
