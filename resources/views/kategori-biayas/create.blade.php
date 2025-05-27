@extends('layouts.home')
@section('title_page', 'Tambah Kategori Biaya')
@section('content')
    <div class="container mt-4">
        <h4>Tambah Kategori Biaya</h4>
        <form action="{{ route('kategori-biayas.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="tahunan">Tahunan</option>
                    <option value="eksidental">Eksidental</option>
                    <option value="tambahan">Tambahan</option>
                    <option value="jalur">Jalur</option>
                </select>
            </div>
            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('kategori-biayas.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
