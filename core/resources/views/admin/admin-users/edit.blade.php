@extends('admin.layouts.app')
@include('components.flash-toast')
@section('panel')
    <h5 class="mb-3">Edit Admin: {{ $admin->username }}</h5>

    <form action="{{ route('admin.admin-users.update',$admin) }}" method="POST" class="card p-4">
        @method('PUT')
        @include('admin.admin-users._form')
        <button class="btn btn-primary">Update</button>
    </form>
@endsection
