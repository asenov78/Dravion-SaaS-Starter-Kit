<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale')
            ?? optional($request->user())->locale
            ?? config('app.locale', 'en');

        app()->setLocale($locale);

        return $next($request);
    }
}
