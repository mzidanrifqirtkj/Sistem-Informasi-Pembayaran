@extends('layouts.home')
@section('title_page', 'Bayar Syahriah/SPP Santri')
@section('content')

    @if (Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ Session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('kategori.store') }}" method="post">
        @csrf
        @method('PUT')
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nama_kategori">Nama Kategori</label>
                        <input type="text" class="form-control @error('nama_kategori') is-invalid @enderror"
                            name="nama_kategori" value="{{ old('nama_kategori', $data->nama_kategori) }}">
                        @error('nama_kategori')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nominal_syahriyah">Biaya Syahriyah</label>
                        <input type="number" class="form-control @error('nominal_syahriyah') is-invalid @enderror"
                            name="nominal_syahriyah" value="{{ old('nominal_syahriyah', $data->nominal_syahriyah) }}">
                        @error('nominal_syahriyah')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <button class="btn btn-primary">Edit</button>
                        <a href="{{ route('biaya_terjadwal.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
