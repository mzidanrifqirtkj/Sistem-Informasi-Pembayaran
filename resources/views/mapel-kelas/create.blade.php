@extends('layouts.home')
@section('title_page', 'Tambah Mapel Kelas')
@section('content')
    <div class="container">
        <form action="{{ route('mapel_kelas.store') }}" method="POST">
            @csrf

            <!-- Pilih Qori -->
            <div class="mb-3">
                <label for="qoriKelas" class="form-label">Qori</label>
                <select id="qoriKelas" name="qori_id" class="form-control" required>
                    <option value="">-- Pilih Qori --</option>
                    @foreach ($qoriKelas as $qori)
                        @if ($qori->santri)
                            <option value="{{ $qori->id_qori_kelas }}">
                                {{ $qori->santri->nama_santri }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Pilih Tahun Ajar -->
            <div class="mb-3">
                <label for="tahunAjar" class="form-label">Tahun Ajar</label>
                <select id="tahunAjar" name="tahun_ajar_id" class="form-control" required>
                    <option value="">-- Pilih Tahun Ajar --</option>
                    @foreach ($tahunAjar as $tahun)
                        <option value="{{ $tahun->id_tahun_ajar }}">
                            {{ $tahun->tahun_ajar }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Kelas -->
            <div class="mb-3">
                <label for="kelas" class="form-label">Kelas</label>
                <select id="kelas" name="kelas_id" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id_kelas }}">
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jam Mulai -->
            <div class="mb-3">
                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                <input type="time" id="jam_mulai" name="jam_mulai" class="form-control" required>
            </div>

            <!-- Jam Selesai -->
            <div class="mb-3">
                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                <input type="time" id="jam_selesai" name="jam_selesai" class="form-control" required>
            </div>

            <!-- Pilih Pelajaran -->
            <div class="mb-3">
                <label for="mapel_id" class="form-label">Pilih Pelajaran</label>
                <select id="mapel_id" name="mapel_id" class="form-control" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach ($mapel as $m)
                        <option value="{{ $m->id_mapel }}">
                            {{ $m->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('mapel_kelas.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
