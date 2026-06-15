<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $sessions = collect();

        if (config('session.driver') === 'database') {
            $sessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderByDesc('last_activity')
                ->get()
                ->map(function ($s) use ($request) {
                    return (object) [
                        'id'            => $s->id,
                        'ip_address'    => $s->ip_address,
                        'user_agent'    => $s->user_agent,
                        'last_activity' => \Carbon\Carbon::createFromTimestamp($s->last_activity),
                        'is_current'    => $s->id === $request->session()->getId(),
                    ];
                });
        }

        return view('sessions', compact('sessions'));
    }

    public function logoutOthers(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => __('auth.password')]);
        }

        Auth::logoutOtherDevices($request->password);

        if (config('session.driver') === 'database') {
            DB::table('sessions')
                ->where('user_id', $request->user()->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return redirect()->route('sessions.index')
            ->with('success', __('flash.sessions_cleared'));
    }
}