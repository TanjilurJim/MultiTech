{{-- resources/views/admin/roles/create.blade.php --}}
@extends('admin.layouts.app')

@section('panel')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="las la-shield-alt me-1"></i> Create Role</h5>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-secondary">
            <i class="las la-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="card-body">

            {{-- role name --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                <input  type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- permissions matrix --}}
            <h6 class="fw-bold mt-4 mb-2">Assign Permissions</h6>
            @include('admin.roles._matrix')   {{-- ← NEW --}}

        </div>

        <div class="card-footer text-end">
            <button class="btn btn-primary">
                <i class="las la-save me-1"></i> Save
            </button>
        </div>
    </form>
</div>

<x-flash-toast/>
@endsection
