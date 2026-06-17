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
            $dbWorks = false;
            try {
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                $dbWorks = true;
            } catch (\Throwable) {
                // DB unavailable — lock is stale, allow reinstall
            }

            if ($dbWorks) {
                abort(404);
            }
        }

        return $next($request);
    }
}
