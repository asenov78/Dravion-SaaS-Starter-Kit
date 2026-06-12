<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name'        => Setting::get('app_name', config('app.name')),
            'app_url'         => Setting::get('app_url', config('app.url')),
            'mail_from'       => Setting::get('mail_from', ''),
            'mail_from_name'  => Setting::get('mail_from_name', ''),
            'registration'    => Setting::get('registration', '1'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name'  => 'required|string|max:100',
            'app_url'   => 'required|url',
            'mail_from' => 'nullable|email',
            'mail_from_name' => 'nullable|string|max:100',
        ]);

        Setting::setMany([
            'app_name'       => $request->app_name,
            'app_url'        => $request->app_url,
            'mail_from'      => $request->mail_from,
            'mail_from_name' => $request->mail_from_name,
            'registration'   => $request->boolean('registration') ? '1' : '0',
        ]);

        return redirect()->route('admin.settings')->with('success', 'Settings saved.');
    }
}
