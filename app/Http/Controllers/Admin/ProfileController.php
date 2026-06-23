<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\AvatarServiceInterface;
use App\Http\Controllers\Controller;
use App\Facades\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PragmaRX\Google2FA\Google2FA;

class ProfileController extends Controller
{
    public function __construct(private AvatarServiceInterface $avatar) {}

    public function show()
    {
        $user   = auth()->user();
        $qrUrl  = null;

        if (! $user->two_factor_confirmed_at) {
            $google2fa = new Google2FA();
            if (! $user->two_factor_secret) {
                $user->update(['two_factor_secret' => $google2fa->generateSecretKey()]);
                $user->refresh();
            }
            $qrUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $user->two_factor_secret);
        }

        return view('admin.showcase.profile', compact('user', 'qrUrl'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'        => 'required|string|max:191',
            'email'       => ['required', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'bio'         => 'nullable|string|max:191',
            'phone'       => 'nullable|string|max:191',
            'country'     => 'nullable|string|max:191',
            'city_state'  => 'nullable|string|max:191',
            'postal_code' => 'nullable|string|max:191',
            'tax_id'      => 'nullable|string|max:191',
            'facebook'    => 'nullable|string|max:191',
            'x_url'       => 'nullable|string|max:191',
            'linkedin'    => 'nullable|string|max:191',
            'instagram'   => 'nullable|string|max:191',
            'avatar'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->avatar->store($request->file('avatar'), $user->avatar);
        } else {
            unset($data['avatar']);
        }

        $user->update($data);

        ActivityLogger::log('profile', 'updated', "Profile updated for {$user->name} ({$user->email})", $user, $user, 'activity.log.profile_updated', ['name' => $user->name, 'email' => $user->email]);

        return redirect()->route('admin.ui.profile')->with('success', __('flash.profile_updated'));
    }
}

