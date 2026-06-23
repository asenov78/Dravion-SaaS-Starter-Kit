<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Require2FA
{
    private const EXEMPT_ROUTES = [
        'profile.two-factor',
        'profile.two-factor.confirm',
        'profile.two-factor.disable',
        'two-factor.challenge',
        'two-factor.verify',
        'logout',
        'verification.notice',
        'verification.verify',
        'verification.send',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (Setting::get('require_2fa', '0') !== '1') {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || $user->two_factor_confirmed_at) {
            return $next($request);
        }

        if ($request->routeIs(...self::EXEMPT_ROUTES)) {
            return $next($request);
        }

        return redirect()->route('profile.two-factor')
            ->with('warning', __('auth.2fa_required'));
    }
}
