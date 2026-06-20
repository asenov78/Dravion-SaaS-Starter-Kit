<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Facades\ActivityLogger;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles       = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:64|unique:roles,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        ActivityLogger::log('users', 'created', "Role '{$role->name}' created", null, null, 'activity.log.role_created', ['role' => $role->name]);

        return redirect()->route('admin.roles.index')->with('success', __('flash.role_created'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'admin') {
            abort(403, 'Cannot rename admin role.');
        }

        $request->validate([
            'name' => 'required|string|max:64|unique:roles,name,' . $role->id,
        ]);

        $old = $role->name;
        $role->update(['name' => $request->name]);

        ActivityLogger::log('users', 'updated', "Role '{$old}' renamed to '{$role->name}'", null, null, 'activity.log.role_renamed', ['old' => $old, 'new' => $role->name]);

        return redirect()->route('admin.roles.index')->with('success', __('flash.role_renamed'));
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            abort(403, 'Cannot delete admin role.');
        }

        ActivityLogger::log('users', 'deleted', "Role '{$role->name}' deleted", null, null, 'activity.log.role_deleted', ['role' => $role->name]);

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('flash.role_deleted'));
    }

    public function syncPermissions(Request $request)
    {
        $matrix = $request->input('matrix', []);

        $roles = Role::where('name', '!=', 'admin')->get();

        foreach ($roles as $role) {
            $permIds = $matrix[$role->id] ?? [];
            $perms   = Permission::whereIn('id', $permIds)->pluck('name')->toArray();
            $role->syncPermissions($perms);
        }

        ActivityLogger::log('settings', 'updated', 'Permissions matrix updated by ' . auth()->user()->name, null, null, 'activity.log.permissions_updated', ['name' => auth()->user()->name]);

        return redirect()->route('admin.roles.index')->with('success', __('flash.permissions_saved'));
    }
}

