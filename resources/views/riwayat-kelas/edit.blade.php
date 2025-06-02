@extends('layouts.home')

@section('title_page', 'Edit Riwayat Kelas')

@section('content')
    <div class="container">
        <h4>Edit Riwayat Kelas</h4>

        <form action="{{ route('riwayat-kelas.update', $riwayat->id_riwayat_kelas) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label for="santri_id">Santri</label>
                <select name="santri_id" id="santri_id" class="form-control select2">
                    <option value="">-- Pilih Santri --</option>
                    @foreach ($santri as $item)
                        <option value="{{ $item->id_santri }}"
                            {{ $riwayat->santri_id == $item->id_santri ? 'selected' : '' }}>
                            {{ $item->nama_santri }}
                        </option>
                    @endforeach
                </select>
                @error('santri_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="mapel_kelas_id">Mapel - Kelas - Tahun Ajar</label>
                <select name="mapel_kelas_id" id="mapel_kelas_id" class="form-control select2">
                    <option value="">-- Pilih Mapel Kelas --</option>
                    @foreach ($mapelKelas as $item)
                        <option value="{{ $item->id_mapel_kelas }}"
                            {{ $riwayat->mapel_kelas_id == $item->id_mapel_kelas ? 'selected' : '' }}>
                            {{ $item->mataPelajaran->nama_mapel ?? '-' }} -
                            {{ $item->kelas->nama_kelas ?? '-' }} -
                            {{ $item->tahunAjar->tahun_ajar ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('mapel_kelas_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('riwayat-kelas.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
