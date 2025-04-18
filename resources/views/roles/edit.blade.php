@extends('layouts.home')
@section('title_page', 'Edit Role')
@section('content')
    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label>Role Name</label>
                    <input type="text" name="name" value="{{ $role->name }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Permissions</label><br>
                    @foreach ($permissions as $permission)
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                                class="form-check-input">
                            <label class="form-check-label">{{ $permission->name }}</label>
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@endsection
