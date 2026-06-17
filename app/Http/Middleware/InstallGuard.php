<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstallGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (file_exists(storage_path('install.lock'))) {
            // If lock exists but DB is not working, allow reinstall
            try {
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                abort(404);
            } catch (\Throwable) {
                // DB unavailable — lock is stale, allow installer through
            }
        }

        return $next($request);
    }
}
