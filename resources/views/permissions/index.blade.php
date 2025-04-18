@extends('layouts.home')

@section('title_page', 'Daftar Permission')

@section('content')
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('permissions.create') }}" class="btn btn-primary">Tambah Permission</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="permissionsTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Permission</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $key => $permission)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $permission->name }}</td>
                        <td>
                            <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('permissions.destroy', $permission) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
