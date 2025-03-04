@extends('layouts.home')
@section('title_page', 'Data Mapel')
@section('content')

    <form action="{{ route('mapel.update', $mataPelajaran) }}" method="post">
        @csrf
        @method('PUT')
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nama_mapel">{{ __('Nama Mata Pelajaran') }}</label>
                        <input id="nama_mapel" type="nama_mapel" class="form-control @error('nama_mapel') is-invalid @enderror"
                            name="nama_mapel" value="{{ old('nama_mapel', $mataPelajaran->nama_mapel) }}" required
                            autocomplete="nama_mapel">

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
                        <button class="btn btn-primary">Edit</button>
                        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>


@endsection
