<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $locale = session('locale')
                ?? optional($request->user())->locale
                ?? Setting::get('default_language')
                ?? config('app.locale', 'en');
        } catch (\Throwable) {
            $locale = config('app.locale', 'en');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
