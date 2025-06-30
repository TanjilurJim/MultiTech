@extends('admin.layouts.app')

@section('panel')
    <h5 class="mb-3">Add Admin User</h5>

    <form action="{{ route('admin.admin-users.store') }}" method="POST" class="card p-4">
        @include('admin.admin-users._form')
        <button class="btn btn-primary">Save</button>
    </form>
@endsection
