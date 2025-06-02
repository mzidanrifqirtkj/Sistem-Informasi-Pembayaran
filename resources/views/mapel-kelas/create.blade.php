@extends('layouts.home')
@section('title_page', 'Tambah Mata Pelajaran Kelas')

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

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('mapel_kelas.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="kelas_id" class="form-label">Kelas</label>
                <select name="kelas_id" id="kelas_id" class="form-control select2" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ old('kelas_id') == $k->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="tahun_ajar_id" class="form-label">Tahun Ajar</label>
                <select name="tahun_ajar_id" id="tahun_ajar_id" class="form-control select2" required>
                    <option value="">-- Pilih Tahun Ajar --</option>
                    @foreach ($tahunAjar as $ta)
                        <option value="{{ $ta->id_tahun_ajar }}"
                            {{ old('tahun_ajar_id') == $ta->id_tahun_ajar ? 'selected' : '' }}>
                            {{ $ta->tahun_ajar }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="mapel_id" class="form-label">Mata Pelajaran</label>
                <select name="mapel_id" id="mapel_id" class="form-control select2" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach ($mapel as $m)
                        <option value="{{ $m->id_mapel }}" {{ old('mapel_id') == $m->id_mapel ? 'selected' : '' }}>
                            {{ $m->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="qori_id" class="form-label">Qori</label>
                <select name="qori_id" id="qori_id" class="form-control select2" required>
                    <option value="">-- Pilih Qori --</option>
                    @foreach ($qoriKelas as $q)
                        <option value="{{ $q->id_qori_kelas }}"
                            {{ old('qori_id') == $q->id_qori_kelas ? 'selected' : '' }}>
                            {{ $q->santri->nama_santri ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                <input type="time" class="form-control" id="jam_selesai" name="jam_selesai"
                    value="{{ old('jam_selesai') }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('mapel_kelas.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
