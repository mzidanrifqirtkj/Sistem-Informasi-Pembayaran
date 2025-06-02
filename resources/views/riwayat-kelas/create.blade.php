@extends('layouts.home')
@section('title_page', 'Tambah Riwayat Kelas Santri')

@section('content')
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('riwayat-kelas.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="santri_id" class="form-label">Santri</label>
                <select name="santri_id" id="santri_id" class="form-control select2" required>
                    <option value="">-- Pilih Santri --</option>
                    @foreach ($santri as $s)
                        <option value="{{ $s->id_santri }}">{{ $s->nama_santri }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="mapel_kelas_id" class="form-label">Jadwal Mapel</label>
                <select name="mapel_kelas_id" id="mapel_kelas_id" class="form-control select2" required>
                    <option value="">-- Pilih Mapel Kelas --</option>
                    @foreach ($mapelKelas as $mk)
                        <option value="{{ $mk->id_mapel_kelas }}">
                            {{ $mk->mataPelajaran->nama_mapel ?? '-' }} -
                            {{ $mk->kelas->nama_kelas ?? '-' }} -
                            {{ $mk->tahunAjar->tahun_ajar ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('riwayat-kelas.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
