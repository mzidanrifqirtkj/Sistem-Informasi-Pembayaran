@extends('layouts.home')
@section('title_page', 'Data Kelas')
@section('content')

    <form action="{{ route('mapel.store') }}" method="post">
        @csrf
        @method('POST')
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nama_mapel">{{ __('Nama Mata Pelajaran') }}</label>
                        <input id="nama_mapel" type="nama_mapel" class="form-control @error('nama_mapel') is-invalid @enderror"
                            name="nama_mapel" value="{{ old('nama_mapel') }}" required autocomplete="nama_mapel">

                        @error('nama_mapel')
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
                        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>


@endsection
