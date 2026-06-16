<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Reject suspended accounts before any session is written.
        if ($user->status === 'suspended') {
            return back()->withErrors(['email' => 'Your account has been suspended.'])
                ->onlyInput('email');
        }

        // 2FA challenge — store user ID in session, redirect to challenge page
        if ($user->two_factor_confirmed_at) {
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
        return redirect()->route('login');
    }
}
