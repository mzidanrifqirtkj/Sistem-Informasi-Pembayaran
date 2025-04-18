@extends('layouts.home')
@section('title_page', 'Create Role')
@section('content')
    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Role Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Permissions</label><br>
                    @foreach ($permissions as $permission)
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                class="form-check-input">
                            <label class="form-check-label">{{ $permission->name }}</label>
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
            </form>
        </div>
    </div>
@endsection
