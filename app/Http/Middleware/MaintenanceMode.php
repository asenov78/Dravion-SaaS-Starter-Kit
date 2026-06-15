<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if (Setting::get('maintenance', '0') === '1') {
            $user = $request->user();

            if (! $user || ! $user->hasRole('admin')) {
                abort(503, 'We\'ll be back soon. The site is under maintenance.');
            }
        }

        return $next($request);
    }
}
