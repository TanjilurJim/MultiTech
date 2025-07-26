@extends('admin.layouts.app')

@section('panel')
    <h4 class="mb-4 fw-semibold">
        <i class="las la-user-edit me-1"></i> Edit Admin — {{ $admin->username }}
    </h4>

    <div class="card p-4 shadow-sm">
        <form action="{{ route('admin.admin-users.update',$admin) }}" method="POST">
            @method('PUT')
            @include('admin.admin-users._form')
            <br>
            <button class="btn btn-primary">
                <i class="las la-sync-alt me-1"></i> Update
            </button>
        </form>
    </div>
@endsection
