@extends('admin.layouts.app')
@include('components.flash-toast')
@section('panel')
    <h5 class="mb-3">Admin Users</h5>

    <a href="{{ route('admin.admin-users.create') }}"
       class="btn btn-sm btn-primary mb-3">Add Admin</a>

    @include('components.flash-toast')

    <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>Name</th><th>Email</th><th>Roles</th><th>Perms</th><th width="140">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($admins as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->roles->pluck('name')->implode(', ') ?: '—' }}</td>
                    <td>{{ $u->permissions->pluck('name')->implode(', ') ?: '—' }}</td>
                    <td>
                        <a href="{{ route('admin.admin-users.edit',$u) }}"
                           class="btn btn-xs btn-warning">Edit</a>

                        @if (!$u->hasRole('super-admin'))
                            <form class="d-inline"
                                  action="{{ route('admin.admin-users.destroy',$u) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete user?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-danger">Del</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-3">No admin found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $admins->links() }}
@endsection
