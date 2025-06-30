{{-- resources/views/admin/permissions/index.blade.php --}}
@extends('admin.layouts.app')

@section('panel')
    {{-- ── Page header / breadcrumb ───────────────────────────────────── --}}
    <div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center">
        <h6 class="page-title mb-0">{{ __('Permissions') }}</h6>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.permissions.create') }}"
               class="btn btn--sm btn--primary">
                <i class="la la-plus"></i> @lang('Add Permission')
            </a>
            @stack('breadcrumb-plugins')
        </div>
    </div>

    {{-- ── Table ─────────────────────────────────────────────────────── --}}
    <div class="table-responsive--md table-responsive table-sm">
        <table class="table table--light style--two">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th class="text-end">@lang('Action')</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($perms as $perm)
                    <tr>
                        <td>{{ $perm->name }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.permissions.edit', $perm) }}"
                               class="btn btn--xs btn--warning">
                                <i class="la la-pencil"></i> @lang('Edit')
                            </a>

                            <form action="{{ route('admin.permissions.destroy', $perm) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('@lang('Delete this permission?')');">
                                @csrf @method('DELETE')
                                <button class="btn btn--xs btn--danger">
                                    <i class="la la-trash"></i> @lang('Del')
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- pagination --}}
    {{ $perms->links() }}
    <x-flash-toast />
@endsection
