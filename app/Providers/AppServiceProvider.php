<?php

namespace App\Providers;

use App\Translation\DatabaseLoader;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->extend('translation.loader', function ($original, $app) {
            return new DatabaseLoader($app['files'], $app['path.lang']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
    }
}
