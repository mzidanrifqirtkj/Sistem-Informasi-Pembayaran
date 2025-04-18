@extends('layouts.home')

@section('title_page', 'Tambah Permission')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Permission Baru</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nama Permission:</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Masukkan Nama Permission" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Simpan Permission</button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary mt-3">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
