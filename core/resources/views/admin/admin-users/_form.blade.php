{{-- resources/views/admin/admin-users/_form.blade.php --}}
@csrf
<div class="mb-3">
    <label class="form-label">Name</label>
    <input name="name" type="text" class="form-control" value="{{ old('name', $admin->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input name="email" type="email" class="form-control" value="{{ old('email', $admin->email ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Username</label>
    <input name="username" type="text" class="form-control" value="{{ old('username', $admin->username ?? '') }}"
        required>
</div>

<div class="mb-3">
    <label class="form-label">Password
        @if (isset($admin))
            <small>(leave blank = keep)</small>
        @endif
    </label>
    <input name="password" type="password" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Confirm Password</label>
    <input name="password_confirmation" type="password" class="form-control">
</div>

<hr>

<div class="mb-2 fw-semibold">Assign Role(s)</div>
@foreach ($roles as $role)
    <label class="me-3">
        <input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked(isset($admin) && $admin->roles->contains('id', $role->id))>
        {{ $role->name }}
    </label>
@endforeach

<hr>

<div class="mb-2 fw-semibold">Extra Permissions</div>
@foreach ($perms as $perm)
    <label class="me-3">
        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" @checked(isset($admin) && $admin->permissions->contains('id', $perm->id))>
        {{ $perm->name }}
    </label>
@endforeach
