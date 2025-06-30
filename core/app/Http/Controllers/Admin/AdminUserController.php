<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserController extends Controller
{
    public function __construct()
    {
        // super-admin OR anyone having   manage admins   permission
        $this->middleware(['role:super-admin|permission:manage admins']);
    }

    /* ------------ READ ------------ */
    public function index()
    {
        $pageTitle = 'All User';
        $admins = Admin::with(['roles', 'permissions'])
            ->latest()
            ->paginate(20);

        return view('admin.admin-users.index', compact('admins', 'pageTitle'));
    }

    /* ------------ CREATE ------------ */
    public function create()
    {
        $pageTitle = 'User Create';
        return view(
            'admin.admin-users.create',
            [
                'roles'  => Role::whereGuardName('admin')->get(),
                'perms'  => Permission::whereGuardName('admin')->get(),
            ],
            compact('pageTitle')
        );
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'        => 'required|string|max:100',
            'email'       => ['required', 'email', 'max:100', 'unique:admins,email'],
            'username'    => ['required', 'alpha_dash', 'max:50', 'unique:admins,username'],
            'password'    => 'required|min:6|confirmed',

            'roles'        => ['array'],
            'roles.*'      => Rule::exists('roles', 'id')->where('guard_name', 'admin'),

            'permissions'  => ['array'],
            'permissions.*' => Rule::exists('permissions', 'id')->where('guard_name', 'admin'),
        ]);

        $admin = Admin::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
        ]);

        /* 🔸 convert posted IDs → Role / Permission models */
        $roleModels = Role::whereIn('id', $data['roles']        ?? [])->get();
        $permModels = Permission::whereIn('id', $data['permissions'] ?? [])->get();

        $admin->syncRoles($roleModels);
        $admin->syncPermissions($permModels);

        return to_route('admin.admin-users.index')
            ->withSuccess('Admin user created');
    }

    /* ------------ UPDATE ------------ */
    public function edit(Admin $admin)
    {
        return view(
            'admin.admin-users.edit',
            [
                'admin' => $admin->load(['roles', 'permissions']),
                'roles' => Role::whereGuardName('admin')->get(),
                'perms' => Permission::whereGuardName('admin')->get(),
            ]
        );
    }

    public function update(Request $r, Admin $admin)
    {
        $data = $r->validate([
            'name'     => 'required|string|max:100',
            'email'    => [
                'required',
                'email',
                'max:100',
                Rule::unique('admins', 'email')->ignore($admin->id)
            ],
            'username' => [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('admins', 'username')->ignore($admin->id)
            ],
            'password' => 'nullable|min:6|confirmed',

            'roles'        => ['array'],
            'roles.*'      => Rule::exists('roles', 'id')->where('guard_name', 'admin'),
            'permissions'  => ['array'],
            'permissions.*' => Rule::exists('permissions', 'id')->where('guard_name', 'admin'),
        ]);

        // ─── basic fields ──────────────────────────────────────────────────────
        $admin->update(collect($data)->only('name', 'email', 'username')->toArray());

        if (filled($data['password'] ?? null)) {
            $admin->update(['password' => bcrypt($data['password'])]);
        }

        // ─── convert the posted IDs to real models (or role names) ────────────
        $roleModels = Role::whereIn('id', $data['roles']        ?? [])->get();
        $permModels = Permission::whereIn('id', $data['permissions'] ?? [])->get();

        $admin->syncRoles($roleModels);        // ← no “RoleDoesNotExist” anymore
        $admin->syncPermissions($permModels);

        return back()->withSuccess('Admin user updated');
    }

    /* ------------ DELETE ------------ */
    public function destroy(Admin $admin)
    {
        abort_if($admin->hasRole('super-admin'), 403);   // keep at least 1 SA
        $admin->delete();

        return back()->withSuccess('Admin user removed');
    }
}
