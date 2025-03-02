@extends('layouts.home')
@section('title_page', 'Edit Absensi')
@section('content')

    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('absensi.update', $absensi->id_absensi) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Tampilkan pesan error validasi -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Dropdown NIS -->
                <div class="form-group">
                    <label for="nis">NIS</label>
                    <select name="nis" id="nis" class="form-control" required>
                        @foreach ($santris as $santri)
                            <option value="{{ $santri->nis }}" {{ $absensi->nis == $santri->nis ? 'selected' : '' }}>
                                {{ $santri->nis }} - {{ $santri->nama_santri }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Bulan -->
                <div class="form-group">
                    <label for="bulan">Bulan</label>
                    <select name="bulan" id="bulan" class="form-control" required>
                        @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $bulan)
                            <option value="{{ $bulan }}" {{ $absensi->bulan == $bulan ? 'selected' : '' }}>
                                {{ $bulan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Minggu per Bulan -->
                <div class="form-group">
                    <label for="minggu_per_bulan">Minggu</label>
                    <select name="minggu_per_bulan" id="minggu_per_bulan" class="form-control" required>
                        @foreach (['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5'] as $minggu)
                            <option value="{{ $minggu }}"
                                {{ $absensi->minggu_per_bulan == $minggu ? 'selected' : '' }}>
                                {{ $minggu }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Input Jumlah Hadir -->
                <div class="form-group">
                    <label for="jumlah_hadir">Hadir</label>
                    <input type="number" name="jumlah_hadir" id="jumlah_hadir" class="form-control"
                        value="{{ $absensi->jumlah_hadir }}" required>
                </div>

                <!-- Input Jumlah Izin -->
                <div class="form-group">
                    <label for="jumlah_izin">Izin</label>
                    <input type="number" name="jumlah_izin" id="jumlah_izin" class="form-control"
                        value="{{ $absensi->jumlah_izin }}" required>
                </div>

                <!-- Input Jumlah Sakit -->
                <div class="form-group">
                    <label for="jumlah_sakit">Sakit</label>
                    <input type="number" name="jumlah_sakit" id="jumlah_sakit" class="form-control"
                        value="{{ $absensi->jumlah_sakit }}" required>
                </div>

                <!-- Input Jumlah Alpha -->
                <div class="form-group">
                    <label for="jumlah_alpha">Alpha</label>
                    <input type="number" name="jumlah_alpha" id="jumlah_alpha" class="form-control"
                        value="{{ $absensi->jumlah_alpha }}" required>
                </div>

                <!-- Dropdown Tahun Ajar -->
                <div class="form-group">
                    <label for="tahun_ajar_id">Tahun Ajar</label>
                    <select name="tahun_ajar_id" id="tahun_ajar_id" class="form-control" required>
                        @foreach ($tahunAjar as $tahun)
                            <option value="{{ $tahun->id_tahun_ajar }}"
                                {{ $absensi->tahun_ajar_id == $tahun->id_tahun_ajar ? 'selected' : '' }}>
                                {{ $tahun->tahun_ajar }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Kelas -->
                <div class="form-group">
                    <label for="kelas_id">Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="form-control" required>
                        @foreach ($kelas as $kelasItem)
                            <option value="{{ $kelasItem->id_kelas }}"
                                {{ $absensi->kelas_id == $kelasItem->id_kelas ? 'selected' : '' }}>
                                {{ $kelasItem->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Submit dan Kembali -->
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection
