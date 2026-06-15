<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SmtpTestMail;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $timezones = \DateTimeZone::listIdentifiers();

        $settings = [
            'app_name'               => Setting::get('app_name', config('app.name')),
            'app_url'                => Setting::get('app_url', config('app.url')),
            'mail_from'              => Setting::get('mail_from', ''),
            'mail_from_name'         => Setting::get('mail_from_name', ''),
            'mail_welcome'           => Setting::get('mail_welcome', '1'),
            'registration'           => Setting::get('registration', '1'),
            'timezone'               => Setting::get('timezone', 'UTC'),
            'date_format'            => Setting::get('date_format', 'd/m/Y'),
            'maintenance'            => Setting::get('maintenance', '0'),
            'logo'                   => Setting::get('logo', ''),
            'default_language'       => Setting::get('default_language', 'en'),
            'activity_log_auth'      => Setting::get('activity_log_auth', '1'),
            'activity_log_users'     => Setting::get('activity_log_users', '1'),
            'activity_log_profile'   => Setting::get('activity_log_profile', '1'),
            'activity_log_settings'  => Setting::get('activity_log_settings', '1'),
            'broadcast_banner'       => Setting::get('broadcast_banner', ''),
        ];

        $availableLocales = ['en' => 'English', 'bg' => 'Български'];

        return view('admin.settings', compact('settings', 'timezones', 'availableLocales'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name'       => 'required|string|max:100',
            'app_url'        => 'required|url',
            'mail_from'      => 'nullable|email',
            'mail_from_name' => 'nullable|string|max:100',
            'timezone'         => 'nullable|string|timezone',
            'date_format'      => 'nullable|string|max:20',
            'default_language'  => 'nullable|in:en,bg',
            'logo'              => 'nullable|image|max:2048',
            'broadcast_banner'  => 'nullable|string|max:500',
        ]);

        $logoPath = Setting::get('logo', '');
        if ($request->hasFile('logo')) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        ActivityLogger::log('settings', 'updated', 'System settings updated by ' . auth()->user()->name, null, null, 'activity.log.settings_updated', ['name' => auth()->user()->name]);

        Setting::setMany([
            'app_name'              => $request->app_name,
            'app_url'               => $request->app_url,
            'mail_from'             => $request->mail_from,
            'mail_from_name'        => $request->mail_from_name,
            'mail_welcome'          => $request->boolean('mail_welcome') ? '1' : '0',
            'registration'          => $request->boolean('registration') ? '1' : '0',
            'timezone'              => $request->input('timezone', 'UTC'),
            'date_format'           => $request->input('date_format', 'd/m/Y'),
            'maintenance'           => $request->boolean('maintenance') ? '1' : '0',
            'logo'                  => $logoPath,
            'default_language'      => $request->input('default_language', 'en'),
            'activity_log_auth'     => $request->boolean('activity_log_auth') ? '1' : '0',
            'activity_log_users'    => $request->boolean('activity_log_users') ? '1' : '0',
            'activity_log_profile'  => $request->boolean('activity_log_profile') ? '1' : '0',
            'activity_log_settings' => $request->boolean('activity_log_settings') ? '1' : '0',
            'broadcast_banner'      => $request->input('broadcast_banner', ''),
        ]);

        return redirect()->route('admin.settings')->with('success', __('flash.settings_saved'));
    }

    public function smtpTest(Request $request)
    {
        try {
            Mail::to(auth()->user()->email)->send(new SmtpTestMail());
            return response()->json(['ok' => true, 'message' => __('settings.smtp_test_ok')]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()]);
        }
    }
}
