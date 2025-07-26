{{-- resources/views/admin/roles/edit.blade.php --}}
@extends('admin.layouts.app')

@section('panel')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="las la-pen me-1"></i> Edit Role — <span class="text-primary">{{ $role->name }}</span>
        </h5>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-secondary">
            <i class="las la-arrow-left"></i> Back
        </a>
    </div>

    {{-- ---------- UPDATE FORM ---------- --}}
    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">
            {{-- role name --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Role Name <span class="text-danger">*</span>
                </label>
                <input  type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $role->name) }}"
                        required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <h6 class="fw-bold mt-4 mb-2">Permissions</h6>
            @include('admin.roles._matrix')
        </div>

        <div class="card-footer text-end">
            <button class="btn btn-primary">
                <i class="las la-sync-alt me-1"></i> Update
            </button>
        </div>
    </form>

    {{-- ---------- DELETE FORM (separate) ---------- --}}
    @if ($role->name !== 'super-admin')
        <form  action="{{ route('admin.roles.destroy', $role) }}"
               method="POST"
               class="mt-2"
               onsubmit="return confirm('Delete this role?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger btn-sm">
                <i class="las la-trash me-1"></i> Delete
            </button>
        </form>
    @endif
</div>

<x-flash-toast/>
@endsection
