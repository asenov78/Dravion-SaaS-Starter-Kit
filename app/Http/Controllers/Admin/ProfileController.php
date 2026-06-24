<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\AvatarServiceInterface;
use App\Http\Controllers\Controller;
use App\Facades\ActivityLogger;
use App\Models\CustomCategory;
use App\Models\CustomField;
use App\Models\UserFieldValue;
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

        $customCategories = CustomCategory::where('entity', 'users')
            ->where('key', '!=', 'account')
            ->with(['fields' => fn($q) => $q->where('is_visible', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $fieldValues = UserFieldValue::where('user_id', $user->id)
            ->pluck('value', 'field_id');

        return view('admin.showcase.profile', compact('user', 'qrUrl', 'customCategories', 'fieldValues'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'       => 'required|string|max:191',
            'email'      => ['required', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'phone'      => 'sometimes|nullable|string|max:191',
            'country'    => 'sometimes|nullable|string|max:191',
            'city_state' => 'sometimes|nullable|string|max:191',
            'avatar'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->avatar->store($request->file('avatar'), $user->avatar);
        } else {
            unset($data['avatar']);
        }

        $user->update($data);

        // Save custom field values
        $customFields = CustomField::where('is_visible', true)
            ->whereHas('category', fn($q) => $q->where('entity', 'users')->where('key', '!=', 'account'))
            ->get();

        foreach ($customFields as $field) {
            $inputKey = "field_{$field->id}";
            if ($request->has($inputKey)) {
                $raw   = $request->input($inputKey);
                $value = is_array($raw) ? implode(',', array_filter($raw)) : $raw;
                UserFieldValue::updateOrCreate(
                    ['user_id' => $user->id, 'field_id' => $field->id],
                    ['value'   => $value]
                );
            }
        }

        ActivityLogger::log('profile', 'updated', "Profile updated for {$user->name} ({$user->email})", $user, $user, 'activity.log.profile_updated', ['name' => $user->name, 'email' => $user->email]);

        return redirect()->route('admin.ui.profile')->with('success', __('flash.profile_updated'));
    }
}
