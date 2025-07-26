@extends('admin.layouts.app')

@section('panel')
    <h4 class="mb-4 fw-semibold d-flex justify-content-between align-items-center">
        <i class="las la-user-plus me-1"></i> 
         <a href="{{ route('admin.admin-users.index') }}" class="btn btn-sm btn-primary">
                    <i class="las la-minus-circle me-1"></i> Back
                </a>
    </h4>

    <div class="card p-4 shadow-sm">
        <form action="{{ route('admin.admin-users.store') }}" method="POST">
            @include('admin.admin-users._form')
            <br>
            <button class="btn btn-primary">
                <i class="las la-save me-1"></i> Save
            </button>
        </form>
    </div>
@endsection
