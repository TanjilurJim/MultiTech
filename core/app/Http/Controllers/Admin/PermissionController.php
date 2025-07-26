<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $perms = Permission::whereGuardName('admin')->paginate(10);
        $pageTitle = 'Permissions';
        return view('admin.permissions.index', compact('perms', 'pageTitle'));
    }

    public function create()
    {
        $pageTitle = 'Permission Create';
        return view('admin.permissions.create', compact('pageTitle'));
    }

    public function edit(Permission $permission)
    {
        $pageTitle = 'Permission Edit';
        return view('admin.permissions.edit', compact('permission', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('permissions')->where('guard_name', 'admin'),
            ],
        ]);

        Permission::create(['name' => $data['name'], 'guard_name' => 'admin']);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission added');
    }

    public function destroy(Permission $permission)
    {
        abort_if($permission->guard_name !== 'admin', 404);

        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission removed');
    }
    // app/Http/Controllers/Admin/PermissionController.php
    public function update(Request $request, Permission $permission)
    {
        abort_if($permission->guard_name !== 'admin', 404);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                /* same-name allowed on other guards, but not on “admin” */
                Rule::unique('permissions')
                    ->ignore($permission->id)
                    ->where('guard_name', 'admin'),
            ],
        ]);

        $permission->update($data);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission updated');
    }
}
