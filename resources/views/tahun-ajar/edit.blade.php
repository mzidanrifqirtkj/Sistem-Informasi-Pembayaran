@extends('layouts.home')
@section('title_page','Data Tahun Ajar')
@section('content')

<form action="{{ route('admin.tahun_ajar.update', $tahunAjar) }}" method="post">
    @csrf
    @method('PUT')
    <div class="container">
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="tahun_ajar">{{ __('Periode Tahun Ajar') }}</label>
                    <input id="tahun_ajar" type="tahun_ajar" class="form-control @error('tahun_ajar') is-invalid @enderror" name="tahun_ajar" value="{{ old('tahun_ajar', $tahunAjar->tahun_ajar) }}" required autocomplete="tahun_ajar" autofocus>

                    @error('tahun_ajar')
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
                    <a href="{{ route('admin.tahun_ajar.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</form>


@endsection
