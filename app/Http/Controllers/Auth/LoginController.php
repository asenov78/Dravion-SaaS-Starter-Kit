<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (Auth::user()->status === 'suspended') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been suspended.']);
            }

            $request->session()->regenerate();

            ActivityLogger::log('auth', 'login', Auth::user()->name . ' logged in', Auth::user(), Auth::user(), 'activity.log.user_logged_in', ['name' => Auth::user()->name]);

            $home = Auth::user()->hasAnyRole(['admin', 'manager'])
                ? route('admin.dashboard')
                : route('dashboard');
            return redirect()->intended($home);
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
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
