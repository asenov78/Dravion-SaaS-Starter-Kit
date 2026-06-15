<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($request->current_password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', __('flash.password_updated'));
    }

    public function updateLocale(Request $request)
    {
        $request->validate(['locale' => 'required|string|max:10']);

        $locale = $request->locale;

        if (Language::where('code', $locale)->exists()) {
            $request->user()->update(['locale' => $locale]);
            session(['locale' => $locale]);
        }

        return redirect()->back()->with('success', __('flash.language_preference_saved'));
    }
}
