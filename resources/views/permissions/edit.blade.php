@extends('layouts.home')

@section('title_page', 'Edit Permission')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Permission</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('permissions.update', $permission) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Nama Permission:</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ $permission->name }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Update Permission</button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary mt-3">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
