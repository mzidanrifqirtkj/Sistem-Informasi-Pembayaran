@extends('layouts.home')
@section('title_page', 'Edit Biaya Pembayaran Pesantren')
@section('content')

    <form action="{{ route('biaya_terjadwal.store') }}" method="post">
        @csrf
        @method('POST')
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nama_biaya">Nama Biaya</label>
                        <input id="nama_biaya" type="text" class="form-control @error('nama_biaya') is-invalid @enderror"
                            name="nama_biaya" value="{{ old('nama_biaya') }}" required autocomplete="nama_biaya">

                        @error('nama_biaya')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input id="nominal" type="number" class="form-control @error('nominal') is-invalid @enderror"
                            name="nominal" value="{{ old('nominal') }}" required autocomplete="nominal" min=0>

                        @error('nominal')
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
                        <label for="periode">Periode</label>
                        <select class="form-control @error('periode') is-invalid @enderror" name="periode">
                            <option value="tahunan" {{ old('periode') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                            <option value="sekali" {{ old('periode') == 'sekali' ? 'selected' : '' }}>Sekali</option>
                        </select>
                        @error('periode')
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
                        <button class="btn btn-primary">Store</button>
                        <a href="{{ route('biaya_terjadwal.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
