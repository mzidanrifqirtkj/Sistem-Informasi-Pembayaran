@extends('layouts.home')
@section('title_page','Add ustadz')
@section('content')

<form action="{{ route('admin.ustadz.store') }}" method="post">
    @csrf
    <div class="container">
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="santri_id">Pilih Santri</label>
                    <select class="form-control @error('santri_id') is-invalid @enderror" name="santri_id">
                        <option value="">Pilih Santri</option>
                        @foreach ($santris as $santri)
                        <option value="{{ $santri->id_santri }}">{{ $santri->nama_santri }}</option>
                        @endforeach

                    </select>
                    @error('santri_id')
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
