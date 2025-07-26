@extends('admin.layouts.app')

@section('panel')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">
        <i class="las la-user-shield me-1"></i> Roles
    </h4>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-primary">
        <i class="las la-plus"></i> Add Role
    </a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th># Users</th>
                    <th># Perms</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $r)
                    <tr>
                        <td>{{ $r->name }}</td>
                        <td>{{ $r->users_count }}</td>
                        <td>{{ $r->permissions_count }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.roles.edit', $r) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="las la-pen"></i>
                            </a>
                            @if ($r->name !== 'super-admin')
                                <form action="{{ route('admin.roles.destroy', $r) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this role?')">
                                        <i class="las la-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">– No roles found –</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($roles->hasPages())
        <div class="card-footer">
            {{ $roles->links() }}
        </div>
    @endif
</div>

<x-flash-toast />
@endsection
