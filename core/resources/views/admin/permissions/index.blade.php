@extends('admin.layouts.app')

@section('panel')
    {{-- ── Page header ───────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="mb-0 fw-semibold">
            <i class="las la-key me-1"></i> @lang('Permissions')
        </h4>

        {{-- <a href="{{ route('admin.permissions.create') }}" class="btn btn-sm btn-primary">
            <i class="las la-plus"></i> @lang('Add Permission')
        </a> --}}
    </div>

    {{-- ── Table inside card ─────────────────────────────── --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>@lang('Name')</th>
                        <th class="text-end">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($perms as $perm)
                        <tr>
                            <td>{{ $perm->name }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.permissions.edit', $perm) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="las la-pen"></i>
                                </a>

                                <form action="{{ route('admin.permissions.destroy', $perm) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('@lang('Delete this permission?')');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="las la-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-4">– No permissions found –</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($perms->hasPages())
            <div class="card-footer py-2">
                {{ $perms->links() }}
            </div>
        @endif
    </div>

    <x-flash-toast />
@endsection
