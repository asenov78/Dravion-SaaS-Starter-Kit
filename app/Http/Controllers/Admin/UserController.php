<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\AvatarServiceInterface;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\AccountActivatedNotification;
use App\Notifications\AccountSuspendedNotification;
use App\Notifications\NewUserRegisteredNotification;
use App\Facades\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private AvatarServiceInterface $avatar) {}
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $role    = $request->input('role', '');
        $status  = $request->input('status', '');
        $trashed = $request->boolean('trashed');

        $query = $trashed ? User::onlyTrashed()->with('roles') : User::with('roles');

        $query->when($search, fn ($q) => $q->where(fn ($q2) =>
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%")
            ))
            ->when($role, fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', $role)))
            ->when($status, fn ($q) => $q->where('status', $status));

        $users = $query->latest()->paginate(20)->withQueryString();

        $roles = \Spatie\Permission\Models\Role::orderBy('name')->pluck('name');

        return view('admin.users.index', compact('users', 'search', 'role', 'status', 'trashed', 'roles'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search', '');
        $role   = $request->input('role', '');
        $status = $request->input('status', '');

        $users = User::with('roles')
            ->when($search, fn ($q) => $q->where(fn ($q2) =>
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%")
            ))
            ->when($role, fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', $role)))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="users-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Status', 'Created At']);
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->getRoleNames()->first() ?? '',
                    $user->status,
                    $user->created_at->toDateTimeString(),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function restore(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $user->restore();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.users.index')->with('success', __('flash.user_restored'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:191',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8',
            'role'        => 'required|exists:roles,name',
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
        ]);

        $role = $data['role'];
        unset($data['role']);
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->assignRole($role);

        if (Setting::get('mail_welcome', '1') !== '0') {
            try {
                // Generate a password-reset token so the user sets their own password.
                $token          = Password::createToken($user);
                $setPasswordUrl = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));
                Mail::to($user->email)->send(new WelcomeMail($user, $setPasswordUrl));
            } catch (\Throwable) {}
        }

        try {
            $notification = new NewUserRegisteredNotification($user);
            User::role('admin')->get()->each->notify($notification);
        } catch (\Throwable) {}

        return redirect()->route('admin.users.edit', $user)->with('success', __('flash.user_created'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:191',
            'email'       => ['required', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'password'    => 'nullable|string|min:8',
            'role'        => 'required|exists:roles,name',
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

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $role = $data['role'];
        unset($data['role']);

        $user->update($data);
        $user->syncRoles([$role]);

        return redirect()->route('admin.users.edit', $user)->with('success', __('flash.user_updated'));
    }

    public function suspend(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'Cannot suspend yourself.');
        }

        $user->update(['status' => 'suspended']);
        try { $user->notify(new AccountSuspendedNotification()); } catch (\Throwable) {}

        ActivityLogger::log('users', 'suspended', "Suspended user {$user->name} ({$user->email})", $user, null, 'activity.log.user_suspended', ['name' => $user->name, 'email' => $user->email]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'suspended']);
        }

        return redirect()->route('admin.users.index')->with('success', __('flash.user_suspended'));
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        try { $user->notify(new AccountActivatedNotification()); } catch (\Throwable) {}

        ActivityLogger::log('users', 'activated', "Activated user {$user->name} ({$user->email})", $user, null, 'activity.log.user_activated', ['name' => $user->name, 'email' => $user->email]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'active']);
        }

        return redirect()->route('admin.users.index')->with('success', __('flash.user_activated'));
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:suspend,activate,delete',
            'ids'    => 'required|array',
            'ids.*'  => 'integer',
        ]);

        $ids = collect($data['ids'])->reject(fn ($id) => $id === auth()->id());

        match ($data['action']) {
            'suspend'  => User::whereIn('id', $ids)->update(['status' => 'suspended']),
            'activate' => User::whereIn('id', $ids)->update(['status' => 'active']),
            'delete'   => User::whereIn('id', $ids)->get()->each->delete(),
        };

        ActivityLogger::log('users', 'bulk_' . $data['action'],
            auth()->user()->name . ' bulk ' . $data['action'] . 'd ' . $ids->count() . ' users');

        return redirect()->route('admin.users.index')
            ->with('success', __('flash.bulk_done', ['action' => $data['action'], 'count' => $ids->count()]));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'Cannot delete yourself.');
        }

        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $user->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.users.index')->with('success', __('flash.user_deleted'));
    }
}

