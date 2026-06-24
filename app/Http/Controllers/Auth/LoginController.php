<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Facades\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Find user and verify password BEFORE creating a session.
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            ActivityLogger::log('auth', 'login_failed',
                'Failed login attempt for ' . $credentials['email'],
                $user ?? null,
                $user ?? null,
                'activity.log.login_failed',
                ['email' => $credentials['email']]
            );

            return back()->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        // Reject suspended accounts — return same generic error to avoid credential oracle.
        if ($user->status === 'suspended') {
            ActivityLogger::log('auth', 'login_suspended',
                'Login attempt on suspended account: ' . $credentials['email'],
                $user, $user,
                'activity.log.login_suspended',
                ['email' => $credentials['email']]
            );

            return back()->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        // 2FA challenge — check remember-device cookie first
        if ($user->two_factor_confirmed_at) {
            $days   = (int) Setting::get('2fa_remember_days', '0');
            $cookie = $request->cookie('dravion_2fa_' . $user->id);

            if ($days > 0 && $cookie === '1') {
                // Trusted device — skip challenge, log in directly
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                ActivityLogger::log('auth', 'login', $user->name . ' logged in', $user, $user, 'activity.log.user_logged_in', ['name' => $user->name]);
                $home = $user->hasAnyRole(['admin', 'manager']) ? route('admin.dashboard') : route('dashboard');
                return redirect()->intended($home);
            }

            $request->session()->put('2fa_user_id', $user->id);
            return redirect()->route('two-factor.challenge');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        ActivityLogger::log('auth', 'login', $user->name . ' logged in', $user, $user, 'activity.log.user_logged_in', ['name' => $user->name]);

        $home = $user->hasAnyRole(['admin', 'manager'])
            ? route('admin.dashboard')
            : route('dashboard');

        return redirect()->intended($home);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        ActivityLogger::log('auth', 'logout', ($user?->name ?? 'User') . ' logged out', $user, $user, 'activity.log.user_logged_out', ['name' => $user?->name ?? 'User']);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = redirect()->route('login');
        if ($user) {
            $response->withCookie(Cookie::forget('dravion_2fa_' . $user->id));
        }
        return $response;
    }
}

