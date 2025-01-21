@extends('layouts.home')
@section('title_page','Tagihan Syahriah Santri')
@section('content')

@if (Session::has('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ Session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<form action="{{ route('admin.tagihan_bulanan.store') }}" method="post">
    @csrf
    {{-- @method('POST') --}}
    <div class="container">
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="santri_id">Nama Santri</label>
                    <select class="form-control select2 @error('santri_id') is-invalid @enderror" name="santri_id" required>
                        <option selected disabled>Pilih Santri</option>
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
                    <a href="{{ route('admin.tagihan_bulanan.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
