<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /** List roles */
    public function index()
    {
        $roles = Role::whereGuardName('admin')
                     ->withCount('users', 'permissions')
                     ->paginate(20);
        
        $pageTitle = 'Roles';

        return view('admin.roles.index', compact('roles', 'pageTitle'));
    }

    /** Show create form */
    public function create()
    {
        $permissions = Permission::whereGuardName('admin')->pluck('name', 'id');
        $pageTitle = 'Role Create';
        return view('admin.roles.create', compact('permissions','pageTitle'));
    }

    /** Persist a new role */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:50',
                              Rule::unique('roles')->where('guard_name', 'admin')],
            'permissions' => ['array']
        ]);

        $role = Role::create([
            'name'       => $data['name'],
            'guard_name' => 'admin',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
                         ->withSuccess('Role created');
    }

    /** Show edit form */
    public function edit(Role $role)
    {
        abort_if($role->guard_name !== 'admin', 404);
        $pageTitle = 'Role Edit';
        $permissions    = Permission::whereGuardName('admin')->pluck('name', 'id');
        $assigned       = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'assigned','pageTitle'));
    }

    /** Update role */
    public function update(Request $request, Role $role)
    {
        abort_if($role->guard_name !== 'admin', 404);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:50',
                              Rule::unique('roles')->ignore($role->id)->where('guard_name', 'admin')],
            'permissions' => ['array']
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return back()->withSuccess('Role updated');
    }

    /** Delete role (safeguard super-admin) */
    public function destroy(Role $role)
    {
        abort_if($role->name === 'super-admin', 403, 'Cannot delete this role.');
        $role->delete();

        return back()->withSuccess('Role deleted');
    }
}
