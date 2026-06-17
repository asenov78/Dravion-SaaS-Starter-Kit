<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $maintenance = Setting::get('maintenance', '0');
        } catch (\Throwable) {
            $maintenance = '0';
        }

        if ($maintenance === '1') {
            $user = $request->user();

            if (! $user || ! $user->hasRole('admin')) {
                abort(503, 'We\'ll be back soon. The site is under maintenance.');
            }
        }

        return $next($request);
    }
}
