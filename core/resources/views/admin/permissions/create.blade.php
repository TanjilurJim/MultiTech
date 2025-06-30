{{-- resources/views/admin/permissions/create.blade.php --}}
@extends('admin.layouts.app')

@section('panel')
    <h6 class="page-title mb-3">{{ __('Add Permission') }}</h6>

    <form method="POST" action="{{ route('admin.permissions.store') }}">
        @csrf
        <div class="row g-3">

            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label">@lang('Permission Name')</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name') }}"
                       required>
            </div>

            {{-- Guard (hidden because you only use “admin”) --}}
            <input type="hidden" name="guard_name" value="admin">

            {{-- Submit --}}
            <div class="col-12">
                <button class="btn btn--primary">@lang('Save')</button>
                <a href="{{ route('admin.permissions.index') }}"
                   class="btn btn--secondary ms-2">
                    @lang('Cancel')
                </a>
            </div>
        </div>
    </form>
    <x-flash-toast />
@endsection
