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
    public function index(Request $request)
    {
        $search = $request->input('q');           // query-string ?q=

        $admins = Admin::withoutGlobalScope('onlyActive')
        ->with(['roles', 'permissions', 'creator'])
        
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name',  'like', "%{$search}%")
                        ->orWhere('email',    'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                // ?status=active / inactive
                $q->where('is_active', $request->status === 'active');
            })
            ->latest()
            ->paginate(7)
            ->withQueryString();                  // keep ?q on page links

        $pageTitle = 'Admin Users';
        return view('admin.admin-users.index', compact('admins', 'pageTitle', 'search'));
    }

    /* ------------ CREATE ------------ */
    public function create()
    {
        $pageTitle = 'User Create';

        $locationData = getBangladeshLocationData();

        return view(
            'admin.admin-users.create',
            [
                'roles' => Role::whereGuardName('admin')->pluck('name')->toArray(),
                'perms' => Permission::whereGuardName('admin')->pluck('name')->toArray(),
                'locationData' => $locationData,
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
            'roles.*'      => Rule::exists('roles', 'name')->where('guard_name', 'admin'),

            'permissions'  => ['array'],
            'permissions.*' => Rule::exists('permissions', 'name')->where('guard_name', 'admin'),
            'is_active'   => ['sometimes', 'boolean'],
        ]);

        $admin = Admin::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
            'created_by'  => auth('admin')->id(),
            'is_active'   => $data['is_active'] ?? true,
        ]);

        $admin->syncRoles($data['roles'] ?? []);
        $admin->syncPermissions($data['permissions'] ?? []);

        // dd($r->areas, $r->all());

        // 🔥 save area assignments
        foreach ($r->areas ?? [] as $area) {
            $admin->areaAssignments()->create([
                'division_id' => $area['division_id'],
                'district_id' => $area['district_id'] ?? null,
                'area_name'   => $area['area_name'] ?? null,
            ]);
        }

        return to_route('admin.admin-users.index')
            ->withSuccess('Admin user created');
    }


    /* ------------ UPDATE ------------ */
    public function edit(Admin $admin)
    {
        $pageTitle = 'edit';
        $locationData = getBangladeshLocationData();
        return view(
            'admin.admin-users.edit',
            [

                'admin' => $admin->load(['roles', 'permissions']),
                'roles' => Role::whereGuardName('admin')->pluck('name')->toArray(),
                'perms' => Permission::whereGuardName('admin')->pluck('name')->toArray(),
                'locationData' => $locationData,
            ],
            compact('pageTitle')
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
            'roles.*'      => Rule::exists('roles', 'name')->where('guard_name', 'admin'),
            'permissions'  => ['array'],
            'permissions.*' => Rule::exists('permissions', 'name')->where('guard_name', 'admin'),
            'is_active'   => ['sometimes', 'boolean'],
        ]);

        $admin->update(collect($data)->only('name', 'email', 'username', 'is_active')->toArray());

        if (filled($data['password'] ?? null)) {
            $admin->update(['password' => $data['password']]);
        }

        $admin->syncRoles($data['roles'] ?? []);
        $admin->syncPermissions($data['permissions'] ?? []);

        // 🔥 save area assignments
        $admin->areaAssignments()->delete();
        foreach ($r->areas ?? [] as $area) {

            // ① ignore anything that isn’t a proper array OR has no division
            if (!is_array($area) || empty($area['division_id'])) {
                continue;
            }

            $admin->areaAssignments()->create([
                'division_id' => $area['division_id'],
                'district_id' => $area['district_id'] ?? null,
                'area_name'   => $area['area_name']   ?? null,
            ]);
        }

        // return back()->withSuccess('Admin user updated');

        return redirect()
            ->route('admin.admin-users.index')
            ->with('success', 'Admin User Updated');
    }


    /* ------------ DELETE ------------ */
    public function destroy(Admin $admin)
    {
        abort_if($admin->hasRole('super-admin'), 403);   // keep at least 1 SA
        $admin->delete();

        return back()->withSuccess('Admin user removed');
    }

    public function deactivate(Admin $admin)
    {
        $admin->update(['is_active' => false]);
        return back()->withSuccess('User disabled');
    }

    /** PUT admin-users/{admin}/activate */
    public function activate(Admin $admin)
    {
        $admin->update(['is_active' => true]);
        return back()->withSuccess('User enabled');
    }
}
