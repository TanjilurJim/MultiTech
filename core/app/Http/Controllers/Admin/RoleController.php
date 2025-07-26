<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /* ---------- LIST ---------- */
    public function index()
    {
        $roles = Role::whereGuardName('admin')
            ->withCount('users', 'permissions')
            ->paginate(20);

        $pageTitle = 'Roles';
        return view('admin.roles.index', compact('roles', 'pageTitle'));
    }

    /* ---------- CREATE ---------- */
    public function create()
    {
        $permissions = Permission::whereGuardName('admin')
            ->get()
            ->groupBy(fn($p) => explode('.', $p->name)[0]); // 'customers', 'followup_logs', …

        $abilities  = ['add', 'view', 'edit', 'delete'];
        $pageTitle  = 'Role Create';

        return view('admin.roles.create', compact('permissions', 'abilities', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles')->ignore($role->id ?? null)
                    ->where('guard_name', 'admin')
            ],
            'permissions'   => ['array'],
            'permissions.*' => Rule::exists('permissions', 'name')->where('guard_name', 'admin'),
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'admin']);
        $role->syncPermissions($data['permissions'] ?? []);

        return to_route('admin.roles.index')->withSuccess('Role created.');
    }

    /* ---------- EDIT ---------- */
    public function edit(Role $role)
    {
        abort_if($role->guard_name !== 'admin', 404);

        $permissions = Permission::whereGuardName('admin')
            ->get()
            ->groupBy(fn($p) => explode('.', $p->name)[0]);

        $abilities     = ['add', 'view', 'edit', 'delete'];
        $rolePermIds   = $role->permissions->pluck('id')->all();
        $pageTitle     = 'Role Edit';

        return view('admin.roles.edit', compact(
            'role',
            'permissions',
            'abilities',
            'rolePermIds',
            'pageTitle'
        ));
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->guard_name !== 'admin', 404);

        $data = $request->validate([
            'name'          => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles')->ignore($role->id ?? null)
                    ->where('guard_name', 'admin')
            ],
            'permissions'   => ['array'],
            'permissions.*' => Rule::exists('permissions', 'name')->where('guard_name', 'admin'),
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return to_route('admin.roles.index')->withSuccess('Role created.');
    }

    /* ---------- DELETE ---------- */
    public function destroy(Role $role)
    {
        abort_if($role->name === 'super-admin', 403, 'Cannot delete this role.');
        $role->delete();

        return back()->withSuccess('Role deleted.');
    }
}
