<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|exists:roles,name',
        ]);

        $user->update($request->only('name', 'email'));
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function suspend(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'Cannot suspend yourself.');
        }

        $user->update(['status' => 'suspended']);

        return redirect()->route('admin.users.index')->with('success', 'User suspended.');
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);

        return redirect()->route('admin.users.index')->with('success', 'User activated.');
    }

    public function destroy(string $id)
    {
        // Reserved — soft delete in future slice
    }
}
