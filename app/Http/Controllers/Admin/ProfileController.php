<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Facades\ActivityLogger;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        return view('admin.showcase.profile', ['user' => auth()->user()]);
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
            $data['avatar'] = AvatarService::store($request->file('avatar'), $user->avatar);
        } else {
            unset($data['avatar']);
        }

        $user->update($data);

        ActivityLogger::log('profile', 'updated', "Profile updated for {$user->name} ({$user->email})", $user, $user, 'activity.log.profile_updated', ['name' => $user->name, 'email' => $user->email]);

        return redirect()->route('admin.ui.profile')->with('success', __('flash.profile_updated'));
    }
}

