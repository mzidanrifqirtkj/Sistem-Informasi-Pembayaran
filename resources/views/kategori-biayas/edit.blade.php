@extends('layouts.home')
@section('title_page', 'Edit Kategori Biaya')
@section('content')
    <div class="container mt-2">
        <form action="{{ route('kategori-biayas.update', $kategori->id_kategori_biaya) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" class="form-control" value="{{ $kategori->nama_kategori }}" required>
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="tahunan" {{ $kategori->status == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    <option value="eksidental" {{ $kategori->status == 'eksidental' ? 'selected' : '' }}>Eksidental</option>
                    <option value="tambahan" {{ $kategori->status == 'tambahan' ? 'selected' : '' }}>Tambahan</option>
                    <option value="jalur" {{ $kategori->status == 'jalur' ? 'selected' : '' }}>Jalur</option>
                </select>
            </div>
            <button class="btn btn-success">Update</button>
            <a href="{{ route('kategori-biayas.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
