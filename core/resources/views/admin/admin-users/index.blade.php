@extends('admin.layouts.app')

@section('panel')
    {{-- flash message --}}
    <x-flash-toast />

    <div class="card shadow-sm">
        {{-- START: Professional Header Redesign --}}
        <div class="card-header">
            {{-- ROW 1: Title and Main Action Button --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h4 class="mb-0 fw-semibold">
                    <i class="las la-users me-1"></i> Admin Users
                </h4>
                @can('users.add')
                    <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary">
                        <i class="las la-plus-circle me-1"></i> Add User
                    </a>
                @endcan
            </div>

            <hr>

            {{-- ROW 2: All Filter Controls Grouped Together --}}
            <form method="GET">
                <div class="d-flex flex-wrap align-items-end gap-2">
                    {{-- Search Input --}}
                    <div class="flex-grow-1">
                        <label for="search-input" class="form-label">Search</label>
                        <input type="text" name="q" value="{{ request('q') }}" id="search-input"
                            placeholder="Search by name or email..." class="form-control">
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label for="status-select" class="form-label">Status</label>
                        <select name="status" id="status-select" class="form-select w-auto">
                            <option value="">All Statuses</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Disabled</option>
                        </select>
                    </div>

                    {{-- Filter Button --}}
                    <div>
                        <button type="submit" class="btn btn-secondary">
                            <i class="las la-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
        {{-- END: Redesign --}}



    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($admins as $u)
                        <tr>
                            <td>
                                {{-- VISUAL IMPROVEMENT: User with Avatar --}}
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-secondary text-white" style="width: 32px; height: 32px;">
                                            {{-- Display first initial of the name --}}
                                            {{ substr($u->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2 fw-semibold">{{ $u->name }}</div>
                                </div>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @forelse ($u->roles as $r)
                                    <span class="badge bg-info mb-1">{{ $r->name }}</span>
                                @empty —
                                @endforelse
                            </td>
                            <td>
                                @if ($u->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Disabled</span>
                                @endif
                            </td>

                            <td>{{ $u->creator?->name ?? '—' }}</td>

                            <td>
                                {{-- ALIGNMENT FIX: Group buttons for consistent alignment and spacing --}}
                                <div class="d-flex justify-content-end gap-1">
                                    @can('users.edit')
                                        <a href="{{ route('admin.admin-users.edit', $u) }}" class="btn btn-sm btn-outline-secondary" title="Edit User">
                                            <i class="las la-pen"></i>
                                        </a>
                                    @endcan

                                    @can('users.delete')
                                        @if (!$u->hasRole('super-admin'))
                                            <form action="{{ route('admin.admin-users.destroy', $u) }}"
                                                method="POST" onsubmit="return confirm('Delete user?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Delete User">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    @if ($u->is_active)
                                        <form method="POST" action="{{ route('admin.admin-users.deactivate', $u) }}">
                                            @csrf @method('PUT')
                                            <button class="btn btn-sm btn-outline-warning" title="Deactivate User">
                                                <i class="las la-user-slash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.admin-users.activate', $u) }}">
                                            @csrf @method('PUT')
                                            <button class="btn btn-sm btn-outline-success" title="Activate User">
                                                <i class="las la-user-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">– No admin found –</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- pagination --}}
        @if ($admins->hasPages())
            <div class="card-footer py-2">
                {{-- ALIGNMENT FIX: Ensure pagination is aligned to the right --}}
                <div class="d-flex justify-content-end">
                    {{ $admins->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection