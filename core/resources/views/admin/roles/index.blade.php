@extends('admin.layouts.app')
@section('panel')


    <h5 class="mb-3">Roles</h5>

    <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-primary mb-3">Add Role</a>

    <table class="table table-bordered">
        <thead>
            <th>Name</th>
            <th># Users</th>
            <th># Perms</th>
            <th>Actions</th>
        </thead>
        <tbody>
            @foreach ($roles as $r)
                <tr>
                    <td>{{ $r->name }}</td>
                    <td>{{ $r->users_count }}</td>
                    <td>{{ $r->permissions_count }}</td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $r) }}" class="btn btn-sm btn-warning">Edit</a>
                        @if ($r->name !== 'super-admin')
                            <form action="{{ route('admin.roles.destroy', $r) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Del</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $roles->links() }}

    <x-flash-toast />
@endsection
@push('breadcrumb-plugins')
    <x-search-form />
@endpush