@extends('layouts.home')
@section('title_page', 'Edit Mapel Kelas')
@section('content')
    <div class="container">
        <form action="{{ route('mapel_kelas.update', $mapelKelas->id_mapel_kelas) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Pilih Qori -->
            <div class="mb-3">
                <label for="qoriKelas" class="form-label">Qori</label>
                <select id="qoriKelas" name="qori_id" class="form-control" required>
                    <option value="">-- Pilih Qori --</option>
                    @foreach ($qoriKelas as $qori)
                        @if ($qori->santri)
                            <option value="{{ $qori->id_qori_kelas }}"
                                {{ $mapelKelas->qori_id == $qori->id_qori_kelas ? 'selected' : '' }}>
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
                        <option value="{{ $tahun->id_tahun_ajar }}"
                            {{ $mapelKelas->tahun_ajar_id == $tahun->id_tahun_ajar ? 'selected' : '' }}>
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
                        <option value="{{ $k->id_kelas }}" {{ $mapelKelas->kelas_id == $k->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jam Mulai -->
            <div class="mb-3">
                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                <input type="time" id="jam_mulai" name="jam_mulai" class="form-control"
                    value="{{ \Carbon\Carbon::parse($mapelKelas->jam_mulai)->format('H:i') }}" required>
            </div>

            <!-- Jam Selesai -->
            <div class="mb-3">
                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                <input type="time" id="jam_selesai" name="jam_selesai" class="form-control"
                    value="{{ \Carbon\Carbon::parse($mapelKelas->jam_selesai)->format('H:i') }}" required>
            </div>

            <!-- Pilih Pelajaran -->
            <div class="mb-3">
                <label for="mapel_id" class="form-label">Pilih Pelajaran</label>
                <select id="mapel_id" name="mapel_id" class="form-control" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach ($mapel as $m)
                        <option value="{{ $m->id_mapel }}" {{ $mapelKelas->mapel_id == $m->id_mapel ? 'selected' : '' }}>
                            {{ $m->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('mapel_kelas.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
