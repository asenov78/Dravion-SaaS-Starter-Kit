<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SmtpTestMail;
use App\Models\Setting;
use App\Facades\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $timezones = \DateTimeZone::listIdentifiers();

        $settings = array_map(
            static fn($default) => Setting::get(...(is_array($default) ? $default : [$default])),
            $this->settingSchema()
        );

        $availableLocales = ['en' => 'English', 'bg' => 'Български'];

        return view('admin.settings', compact('settings', 'timezones', 'availableLocales'));
    }

    /** Single source of truth for all setting keys + their defaults. */
    private function settingSchema(): array
    {
        return [
            'app_name'              => ['app_name',             config('app.name')],
            'app_url'               => ['app_url',              config('app.url')],
            'mail_from'             => ['mail_from',            ''],
            'mail_from_name'        => ['mail_from_name',       ''],
            'mail_welcome'          => ['mail_welcome',         '1'],
            'registration'          => ['registration',         '1'],
            'timezone'              => ['timezone',             'UTC'],
            'date_format'           => ['date_format',          'd/m/Y'],
            'maintenance'           => ['maintenance',          '0'],
            'logo'                  => ['logo',                 ''],
            'default_language'      => ['default_language',     'en'],
            'week_start'            => ['week_start',           '1'],
            'activity_log_auth'     => ['activity_log_auth',    '1'],
            'activity_log_users'    => ['activity_log_users',   '1'],
            'activity_log_profile'  => ['activity_log_profile', '1'],
            'activity_log_settings' => ['activity_log_settings','1'],
            'broadcast_banner'      => ['broadcast_banner',     ''],
            'broadcast_banner_bg'   => ['broadcast_banner_bg',  ''],
            'footer_text'           => ['footer_text',          ''],
            'footer_text_bg'        => ['footer_text_bg',       ''],
            'footer_copyright'      => ['footer_copyright',     ''],
            'footer_copyright_bg'   => ['footer_copyright_bg',  ''],
            'header_tagline'        => ['header_tagline',       ''],
            'header_tagline_bg'     => ['header_tagline_bg',    ''],
            'require_2fa'           => ['require_2fa',           '0'],
        ];
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
            'week_start'        => 'nullable|in:0,1',
            'logo'              => 'nullable|image|max:2048',
            'broadcast_banner'    => 'nullable|string|max:500',
            'broadcast_banner_bg' => 'nullable|string|max:500',
            'footer_text'         => 'nullable|string|max:500',
            'footer_text_bg'      => 'nullable|string|max:500',
            'footer_copyright'    => 'nullable|string|max:200',
            'footer_copyright_bg' => 'nullable|string|max:200',
            'header_tagline'      => 'nullable|string|max:200',
            'header_tagline_bg'   => 'nullable|string|max:200',
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
            'week_start'            => $request->input('week_start', '1'),
            'activity_log_auth'     => $request->boolean('activity_log_auth') ? '1' : '0',
            'activity_log_users'    => $request->boolean('activity_log_users') ? '1' : '0',
            'activity_log_profile'  => $request->boolean('activity_log_profile') ? '1' : '0',
            'activity_log_settings' => $request->boolean('activity_log_settings') ? '1' : '0',
            'broadcast_banner'      => $request->input('broadcast_banner', ''),
            'broadcast_banner_bg'   => $request->input('broadcast_banner_bg', ''),
            'footer_text'           => $request->input('footer_text', ''),
            'footer_text_bg'        => $request->input('footer_text_bg', ''),
            'footer_copyright'      => $request->input('footer_copyright', ''),
            'footer_copyright_bg'   => $request->input('footer_copyright_bg', ''),
            'header_tagline'        => $request->input('header_tagline', ''),
            'header_tagline_bg'     => $request->input('header_tagline_bg', ''),
            'require_2fa'           => $request->boolean('require_2fa') ? '1' : '0',
        ]);

        return redirect()->route('admin.settings')->with('success', __('flash.settings_saved'));
    }

    public function smtpTest(Request $request)
    {
        try {
            Mail::to(auth()->user()->email)->send(new SmtpTestMail());
            return response()->json(['ok' => true, 'message' => __('settings.smtp_test_ok')]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SMTP test failed', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => __('settings.smtp_test_fail')]);
        }
    }
}

