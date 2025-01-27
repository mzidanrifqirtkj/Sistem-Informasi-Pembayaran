@extends('layouts.home')
@section('title_page','Bayar Pendaftaran Santri')
@section('content')

<form action="{{ route('admin.tagihan_terjadwal.store') }}" method="post">
    @csrf
    <div class="container">
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="santri_id">Nama Santri</label>
                    <select class="form-control select2 @error('santri_id') is-invalid @enderror" name="santri_id" required>
                        <option selected disabled>Pilih Santri</option>
                        @foreach ($santris as $santri)
                            <option value="{{ $santri->id_santri }}">{{ $santri->nama_santri }}</option>
                                {{-- @if (\App\Models\TagihanTerjadwal::where('santri_id', $santri->id_santri)->exists()) --}}
                                    {{-- disabled --}}
                                {{-- @endif> --}}
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
                    <label for="biaya_terjadwal_id">Biaya Terjadwal</label>
                    <select class="form-control select2 @error('biaya_terjadwal_id') is-invalid @enderror" id="biaya_terjadwal_id" name="biaya_terjadwal_id" required>
                        <option selected disabled>Pilih Biaya</option>
                        @foreach ($biayaTerjadwals as $biaya)
                            <option value="{{ $biaya->id_biaya_terjadwal }}" data-periode="{{ $biaya->periode }}">
                                {{ $biaya->nama_biaya }}
                            </option>
                        @endforeach
                    </select>

                    @error('biaya_terjadwal_id')
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
                    <label for="tahun">Tahun</label>
                    <select class="form-control select2 @error('tahun') is-invalid @enderror" name="tahun" required>
                        <option selected disabled>Pilih Tahun</option>
                        @for ($i = $now; $i >= 2020; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    @error('tahun')
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
                    <button class="btn btn-primary">Buat Tagihan</button>
                    <a href="{{ route('admin.biaya_terjadwal.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>

    </div>
</form>


@endsection
