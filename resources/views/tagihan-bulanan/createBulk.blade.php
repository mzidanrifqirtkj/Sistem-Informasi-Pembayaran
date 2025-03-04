@extends('layouts.home')
@section('title_page', 'Bayar Pendaftaran Santri')
@section('content')

    <form action="{{ route('tagihan_bulanan.bulkBulanan') }}" method="post">
        @csrf
        @method('POST')
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="bulan">Bulan</label>
                    <select class="form-control select2 @error('bulan') is-invalid @enderror" name="bulan" required>
                        <option selected disabled>Pilih Bulan</option>
                        <option value="Jan">Januari</option>
                        <option value="Feb">Februari</option>
                        <option value="Mar">Maret</option>
                        <option value="Apr">April</option>
                        <option value="May">Mei</option>
                        <option value="Jun">Juni</option>
                        <option value="Jul">Juli</option>
                        <option value="Aug">Agustus</option>
                        <option value="Sep">September</option>
                        <option value="Oct">Oktober</option>
                        <option value="Nov">November</option>
                        <option value="Dec">Desember</option>
                    </select>

                    @error('bulan')
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
                        @for ($i = 2020; $i <= date('Y'); $i++)
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
                    <button class="btn btn-primary">Generate Tagihan</button>
                    <a href="{{ route('tagihan_bulanan.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>

    </form>

@endsection


{{-- <div class="container">
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="santri_id">Nama Santri</label>
                    <select class="form-control select2 @error('santri_id') is-invalid @enderror" name="santri_id" required>
                        <option selected disabled>Pilih Santri</option>
                        @foreach ($data as $santri)
                        <option value="{{ $santri->id }}"
@if (\App\Models\RegistrationCost::where('santri_id', $santri->id)->exists())
disabled
@endif>{{ $santri->name }}</option>
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
            <label for="construction">Biaya Pembangunan</label>
            <h5>Rp. {{ number_format($cost->construction, 2, ',', '.') }}</h5>
            <input type="text" hidden class="form-control" name="construction" value="{{ $cost->construction }}" readonly>
        </div>
    </div>
    <div class="col-sm">
        <div class="form-group">
            <label for="facilities">Biaya Fasilitas</label>
            <h5>Rp. {{ number_format($cost->facilities, 2, ',', '.') }}</h5>
            <input type="text" hidden class="form-control" name="facilities" value="{{ $cost->facilities }}" readonly>
        </div>
    </div>
    <div class="col-sm">
        <div class="form-group">
            <label for="wardrobe">Biaya Alokasi Almari</label>
            <h5>Rp. {{ number_format($cost->wardrobe, 2, ',', '.') }}</h5>
            <input type="text" hidden class="form-control" name="wardrobe" value="{{ $cost->wardrobe }}" readonly>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm">
        <div class="form-group">
            <button class="btn btn-primary">Bayar</button>
            <a href="{{ route('registration.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
</div> --}}
