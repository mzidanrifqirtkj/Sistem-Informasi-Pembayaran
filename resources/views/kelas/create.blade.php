@extends('layouts.home')
@section('title_page','Data Kelas')
@section('content')

<form action="{{ route('admin.kelas.store') }}" method="post">
    @csrf
    <div class="container">
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="nama_kelas">{{ __('Nama Kelas') }}</label>
                    <input id="nama_kelas" type="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror" name="nama_kelas" value="{{ old('nama_kelas') }}" required autocomplete="nama_kelas">

                    @error('nama_kelas')
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
                    <button class="btn btn-primary">Tambah</button>
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</form>


@endsection
