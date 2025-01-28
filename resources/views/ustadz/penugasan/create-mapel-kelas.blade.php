@extends('layouts.home')
@section('title_page','Mapel Kelas')
@section('content')
<div class="container">
    <h1 class="text-center mb-4">Kelola Pelajaran Per Kelas</h1>

    <form action="{{ route('admin.ustadz.penugasan.storeMapelKelas') }}" method="POST">
        @csrf

        <!-- Pilih Tahun Ajar -->
        <div class="mb-3">
            <label for="tahunAjar" class="form-label">Tahun Ajar</label>
            <select id="tahunAjar" name="id_tahun_ajar" class="form-control" required>
                <option value="" disabled selected>Pilih Tahun Ajar</option>
                @foreach($tahunAjar as $tahun)
                    <option value="{{ $tahun->id_tahun_ajar }}">{{ $tahun->tahun_ajar }}</option>
                @endforeach
            </select>
        </div>

        <!-- Pilih Kelas -->
        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <select id="kelas" name="id_kelas" class="form-control" required>
                <option value="" disabled selected>Pilih Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <!-- Pilih Pelajaran -->
        <div class="mb-3">
            <label class="form-label">Pilih Pelajaran</label>
            <div>
                @foreach($mapel as $m)
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="mapel{{ $m->id_mapel }}"
                            name="id_mapel[]"
                            value="{{ $m->id_mapel }}"
                        >
                        <label class="form-check-label" for="mapel{{ $m->id_mapel }}">
                            {{ $m->nama_mapel }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Pelajaran</button>
    </form>
</div>
@endsection
