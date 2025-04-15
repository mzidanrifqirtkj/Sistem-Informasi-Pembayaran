@extends('layouts.home')
@section('title_page', 'Absensi Santri')

@section('css_inline')
    <style>
        /* Sticky columns */
        .sticky-col {
            position: sticky;
            left: 0;
            background-color: white;
            z-index: 1;
        }

        .sticky-col-no {
            left: 0;
            min-width: 50px;
        }

        .sticky-col-nis {
            left: 50px;
            min-width: 100px;
        }

        .sticky-col-nama {
            left: 150px;
            min-width: 200px;
        }

        /* Warna untuk status absensi */
        .badge-hadir {
            background-color: #28a745;
            /* Hijau */
            color: white;
        }

        .badge-izin {
            background-color: #ffc107;
            /* Kuning */
            color: black;
        }

        .badge-sakit {
            background-color: #17a2b8;
            /* Biru */
            color: white;
        }

        .badge-alpha {
            background-color: #dc3545;
            /* Merah */
            color: white;
        }

        .badge-secondary {
            background-color: #6c757d;
            /* Abu-abu */
            color: white;
        }

        .badge-weekend {
            background-color: #f8f9fa;
            /* Light gray */
            color: black;
        }

        .badge-light {
            background-color: #f8f9fa;
            /* Light gray */
            color: black;
        }

        /* Scroll horizontal */
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Absensi Santri - {{ $months[$currentMonth] }} {{ $currentYear }}</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form Filter -->
                        <form method="GET" action="{{ route('absensi.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="nama">Nama Santri</label>
                                        <input type="text" class="form-control" id="nama" name="nama"
                                            value="{{ $namaSantri }}" placeholder="Cari nama...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="kelas">Kelas</label>
                                        <select class="form-control" id="kelas" name="kelas">
                                            <option value="">Semua Kelas</option>
                                            @foreach ($kelas as $k)
                                                <option value="{{ $k->id_kelas }}"
                                                    {{ $kelasId == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="bulan">Bulan</label>
                                        <select class="form-control" id="bulan" name="bulan">
                                            @foreach ($months as $key => $month)
                                                <option value="{{ $key }}"
                                                    {{ $currentMonth == $key ? 'selected' : '' }}>{{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="tahun">Tahun</label>
                                        <select class="form-control" id="tahun" name="tahun">
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="tahun_ajar">Tahun Ajar</label>
                                        <select class="form-control" id="tahun_ajar" name="tahun_ajar">
                                            @foreach ($tahunAjars as $ta)
                                                <option value="{{ $ta->id_tahun_ajar }}"
                                                    {{ $tahunAjar->id_tahun_ajar == $ta->id_tahun_ajar ? 'selected' : '' }}>
                                                    {{ $ta->tahun_ajar }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="d-block">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('absensi.laporan') }}" class="btn btn-info">Lihat Laporan</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if (count($santris) > 0)
                            <!-- Form Pengisian Absensi Hari Ini -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Isi Absensi Hari Ini ({{ \Carbon\Carbon::now()->format('d-m-Y') }})</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('absensi.bulk.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tanggal"
                                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                        <input type="hidden" name="tahun_ajar_id" value="{{ $tahunAjar->id_tahun_ajar }}">
                                        <input type="hidden" name="kelas_id" value="{{ $kelasId }}">

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th width="10%">NIS</th>
                                                        <th width="30%">Nama Santri</th>
                                                        <th width="25%">Kelas</th>
                                                        <th width="30%">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($santris as $key => $santri)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $santri->nis }}</td>
                                                            <td>{{ $santri->nama_santri }}</td>
                                                            <td>
                                                                @php
                                                                    $kelasNama =
                                                                        DB::table('absensis')
                                                                            ->join(
                                                                                'kelas',
                                                                                'id_kelas',
                                                                                '=',
                                                                                'absensis.kelas_id',
                                                                            )
                                                                            ->where('nis', $santri->nis)
                                                                            ->where(
                                                                                'tahun_ajar_id',
                                                                                $tahunAjar->id_tahun_ajar,
                                                                            )
                                                                            ->value('nama_kelas') ?? 'Belum Ada Kelas';
                                                                @endphp
                                                                {{ $kelasNama }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $today = \Carbon\Carbon::now()->format('Y-m-d');
                                                                    $absensiToday =
                                                                        $absensis[$santri->nis][
                                                                            \Carbon\Carbon::now()->format('d')
                                                                        ] ?? null;
                                                                    $currentStatus = $absensiToday->status ?? '';
                                                                @endphp
                                                                <div class="btn-group btn-group-toggle"
                                                                    data-toggle="buttons">
                                                                    <label
                                                                        class="btn btn-outline-success {{ $currentStatus == 'hadir' ? 'active' : '' }}">
                                                                        <input type="radio"
                                                                            name="status[{{ $santri->nis }}]"
                                                                            value="hadir"
                                                                            {{ $currentStatus == 'hadir' ? 'checked' : '' }}>
                                                                        Hadir
                                                                    </label>
                                                                    <label
                                                                        class="btn btn-outline-warning {{ $currentStatus == 'izin' ? 'active' : '' }}">
                                                                        <input type="radio"
                                                                            name="status[{{ $santri->nis }}]"
                                                                            value="izin"
                                                                            {{ $currentStatus == 'izin' ? 'checked' : '' }}>
                                                                        Izin
                                                                    </label>
                                                                    <label
                                                                        class="btn btn-outline-info {{ $currentStatus == 'sakit' ? 'active' : '' }}">
                                                                        <input type="radio"
                                                                            name="status[{{ $santri->nis }}]"
                                                                            value="sakit"
                                                                            {{ $currentStatus == 'sakit' ? 'checked' : '' }}>
                                                                        Sakit
                                                                    </label>
                                                                    <label
                                                                        class="btn btn-outline-danger {{ $currentStatus == 'alpha' ? 'active' : '' }}">
                                                                        <input type="radio"
                                                                            name="status[{{ $santri->nis }}]"
                                                                            value="alpha"
                                                                            {{ $currentStatus == 'alpha' ? 'checked' : '' }}>
                                                                        Alpha
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-right mt-3">
                                            <button type="submit" class="btn btn-primary">Simpan Absensi Hari Ini</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Tabel Absensi Bulan Ini -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5>Rekap Absensi Bulan {{ $months[$currentMonth] }} {{ $currentYear }}</h5>
                                    <a href="{{ route('absensi.export', request()->query()) }}"
                                        class="btn btn-sm btn-success">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="sticky-col sticky-col-no" rowspan="2"
                                                        style="vertical-align: middle;">No</th>
                                                    <th class="sticky-col sticky-col-nis" rowspan="2"
                                                        style="vertical-align: middle;">NIS</th>
                                                    <th class="sticky-col sticky-col-nama" rowspan="2"
                                                        style="vertical-align: middle;">Nama Santri</th>
                                                    <th colspan="{{ $daysInMonth }}" class="text-center">Tanggal</th>
                                                </tr>
                                                <tr>
                                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                                        <th class="text-center" width="30px">{{ $i }}</th>
                                                    @endfor
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($santris as $key => $santri)
                                                    <tr>
                                                        <td class="sticky-col sticky-col-no">{{ $key + 1 }}</td>
                                                        <td class="sticky-col sticky-col-nis">{{ $santri->nis }}</td>
                                                        <td class="sticky-col sticky-col-nama">{{ $santri->nama_santri }}
                                                        </td>

                                                        @for ($i = 1; $i <= $daysInMonth; $i++)
                                                            @php
                                                                $day = str_pad($i, 2, '0', STR_PAD_LEFT); // Format hari menjadi 2 digit (01, 02, dst.)
                                                            @endphp
                                                            <td class="text-center">
                                                                @if (isset($absensis[$santri->nis][$day]))
                                                                    @php
                                                                        $status = $absensis[$santri->nis][$day]->status;
                                                                        if ($status == 'hadir') {
                                                                            $badge = 'badge-hadir';
                                                                            $text = 'H';
                                                                        } elseif ($status == 'izin') {
                                                                            $badge = 'badge-izin';
                                                                            $text = 'I';
                                                                        } elseif ($status == 'sakit') {
                                                                            $badge = 'badge-sakit';
                                                                            $text = 'S';
                                                                        } else {
                                                                            $badge = 'badge-alpha';
                                                                            $text = 'A';
                                                                        }
                                                                    @endphp
                                                                    <span
                                                                        class="badge {{ $badge }}">{{ $text }}</span>
                                                                @else
                                                                    @php
                                                                        $date = \Carbon\Carbon::createFromDate(
                                                                            $currentYear,
                                                                            $currentMonth,
                                                                            $i,
                                                                        );
                                                                        $isWeekend =
                                                                            $date->isSaturday() || $date->isSunday();
                                                                        $isPastDate = $date->isPast();
                                                                        $isToday = $date->isToday();
                                                                    @endphp

                                                                    @if ($isPastDate && !$isWeekend && !$isToday)
                                                                        <span class="badge badge-secondary"
                                                                            data-toggle="tooltip"
                                                                            title="Belum diisi">-</span>
                                                                    @elseif($isWeekend)
                                                                        <span class="badge badge-light"
                                                                            data-toggle="tooltip" title="Weekend">W</span>
                                                                    @else
                                                                        <span
                                                                            class="badge
                                                                            badge-light"
                                                                            data-toggle="tooltip"
                                                                            title="Belum waktunya">-</span>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        @endfor
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Tidak ada data santri yang sesuai dengan filter yang dipilih.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Inisialisasi tooltip
            $('[data-toggle="tooltip"]').tooltip();

            // Tombol untuk mengisi absensi per tanggal tertentu
            $('.btn-isi-absensi').on('click', function() {
                let tanggal = $(this).data('tanggal');
                $('#modalTanggal').text(tanggal);
                $('#inputTanggal').val(tanggal);
                $('#modalAbsensi').modal('show');
            });

            // Ajax untuk menyimpan absensi
            $(document).on('change', '.radioAbsensi', function() {
                let nis = $(this).data('nis');
                let status = $(this).val();
                let tanggal = $(this).data('tanggal');
                let kelas_id = $('#kelas').val() || $(this).data('kelas');
                let tahun_ajar_id = $('#tahun_ajar').val();

                $.ajax({
                    url: "{{ route('absensi.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        nis: nis,
                        kelas_id: kelas_id,
                        tanggal: tanggal,
                        status: status,
                        tahun_ajar_id: tahun_ajar_id
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Terjadi kesalahan: ' + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endsection
