<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /** Show 2FA setup page with QR code */
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_confirmed_at) {
            return view('auth.two-factor.manage');
        }

        if (! $user->two_factor_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => $secret]);
        }

        $qrUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        return view('auth.two-factor.setup', compact('qrUrl'));
    }

    /** Confirm TOTP code to enable 2FA */
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = $request->user();

        if (! $user->two_factor_secret) {
            return back()->withErrors(['code' => __('auth.2fa_not_setup')]);
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (! $valid) {
            return back()->withErrors(['code' => __('auth.2fa_invalid_code')]);
        }

        $user->update(['two_factor_confirmed_at' => now()]);

        return redirect()->route('profile.two-factor')->with('success', __('flash.2fa_enabled'));
    }

    /** Disable 2FA */
    public function disable(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        if (! \Illuminate\Support\Facades\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => __('auth.password')]);
        }

        $request->user()->update([
            'two_factor_secret'       => null,
            'two_factor_confirmed_at' => null,
        ]);

        return redirect()->route('profile.two-factor')->with('success', __('flash.2fa_disabled'));
    }

    /** Show TOTP challenge after login */
    public function challenge()
    {
        if (! session('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor.challenge');
    }

    /** Verify TOTP challenge */
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $userId = session('2fa_user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::findOrFail($userId);

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (! $valid) {
            return back()->withErrors(['code' => __('auth.2fa_invalid_code')]);
        }

        \Illuminate\Support\Facades\Auth::login($user);
        $request->session()->forget('2fa_user_id');
        $request->session()->regenerate();

        $home = $user->hasAnyRole(['admin', 'manager']) ? route('admin.dashboard') : route('dashboard');
        return redirect()->intended($home);
    }
}
