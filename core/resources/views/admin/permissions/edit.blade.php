{{-- resources/views/admin/permissions/edit.blade.php --}}
@extends('admin.layouts.app')

@section('panel')
    <h6 class="page-title mb-3">{{ __('Edit Permission') }}</h6>

    <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
        @csrf @method('PUT')

        <div class="row g-3">
            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label">@lang('Permission Name')</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $permission->name) }}"
                       required>
            </div>

            <input type="hidden" name="guard_name" value="admin">

            {{-- Submit --}}
            <div class="col-12">
                <button class="btn btn--primary">@lang('Update')</button>
                <a href="{{ route('admin.permissions.index') }}"
                   class="btn btn--secondary ms-2">
                    @lang('Back')
                </a>
            </div>
        </div>
    </form>
    <x-flash-toast />
@endsection
