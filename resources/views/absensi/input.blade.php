@extends('layouts.home')

@section('content')
    <div class="container">
        <h1>Input Absensi - {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</h1>

        <div class="card">
            <div class="card-header">
                <strong>{{ $mataPelajaranKelas->mataPelajaran->nama }}</strong>
                <br>
                Ustadz: {{ $mataPelajaranKelas->ustadz->nama }}
                <br>
                Jam: {{ $mataPelajaranKelas->jam_mulai }} - {{ $mataPelajaranKelas->jam_selesai }}
            </div>

            <form method="POST" action="{{ route('absensi.simpan') }}">
                @csrf
                <input type="hidden" name="absensi_harian_id" value="{{ $absensiHarian->id }}">
                <input type="hidden" name="mata_pelajaran_kelas_id" value="{{ $mataPelajaranKelas->id }}">

                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all">
                                    Nama Santri
                                </th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($santri as $s)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input tidak-hadir-checkbox"
                                                name="tidak_hadir[]" value="{{ $s->id }}"
                                                id="santri-{{ $s->id }}"
                                                {{ isset($absensiSebelumnya[$s->id]) && $absensiSebelumnya[$s->id] !== 'hadir' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="santri-{{ $s->id }}">
                                                {{ $s->nama }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="alasan[]" class="form-control status-select"
                                            {{ isset($absensiSebelumnya[$s->id]) && $absensiSebelumnya[$s->id] !== 'hadir' ? '' : 'disabled' }}>
                                            <option value="alpa"
                                                {{ isset($absensiSebelumnya[$s->id]) && $absensiSebelumnya[$s->id] == 'alpa' ? 'selected' : '' }}>
                                                Alpa
                                            </option>
                                            <option value="izin"
                                                {{ isset($absensiSebelumnya[$s->id]) && $absensiSebelumnya[$s->id] == 'izin' ? 'selected' : '' }}>
                                                Izin
                                            </option>
                                            <option value="sakit"
                                                {{ isset($absensiSebelumnya[$s->id]) && $absensiSebelumnya[$s->id] == 'sakit' ? 'selected' : '' }}>
                                                Sakit
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                </div>
            </form>
        </div>
    </div>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select All Checkbox
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.tidak-hadir-checkbox');
            const statusSelects = document.querySelectorAll('.status-select');

            selectAll.addEventListener('change', function() {
                checkboxes.forEach((checkbox, index) => {
                    checkbox.checked = this.checked;
                    statusSelects[index].disabled = !this.checked;
                });
            });

            // Individual Checkbox Behavior
            checkboxes.forEach((checkbox, index) => {
                checkbox.addEventListener('change', function() {
                    statusSelects[index].disabled = !this.checked;
                });
            });
        });
    </script>
@endsection
@endsection
