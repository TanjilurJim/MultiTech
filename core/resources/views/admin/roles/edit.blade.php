@extends('admin.layouts.app')
@section('title', 'Edit Role')

@section('panel')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Role — <span class="text-primary">{{ $role->name }}</span></h5>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-secondary">
                <i class="las la-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">

                {{-- Role name --}}
                <div class="mb-3">
                    <label class="form-label">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $role->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Permissions checklist --}}
                <h6 class="fw-bold mb-2 mt-4">Permissions</h6>
                <div class="row">
                    @foreach ($permissions as $id => $perm)
                        <div class="col-md-4 mb-1">
                            <label class="form-check">
                                <input type="checkbox" name="permissions[]" value="{{ $id }}"
                                    class="form-check-input" {{ in_array($id, $assigned) ? 'checked' : '' }}>
                                <span class="form-check-label">{{ $perm }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                    onsubmit="return confirm('Delete this role?');">
                    @csrf @method('DELETE')
                    @if ($role->name !== 'super-admin')
                        <button class="btn btn-danger">
                            <i class="las la-trash"></i> Delete
                        </button>
                    @endif
                </form>

                <button class="btn btn-primary">
                    <i class="las la-save"></i> Update
                </button>
            </div>
        </form>
    </div>
    <x-flash-toast />
@endsection
