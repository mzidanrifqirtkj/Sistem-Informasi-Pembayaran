@extends('layouts.home')
@section('title_page', 'Data Absensi')
@section('content')
    <div class="row">
        <div class="col-md-4 d-flex justify-content-between">
            <a href="{{ route('admin.absensi.create') }}" class="btn btn-primary">Tambah Absensi</a>
            <a href="{{ route('admin.absensi.importForm') }}" class="btn btn-primary">Import Absensi</a>
        </div>
    </div>

    <div class="row align-items-end mb-3">
        <div class="col-md-2">
            <label for="filterKelas">Kelas</label>
            <select id="filterKelas" class="form-control">
                <option value="">Pilih Kelas</option>
                @foreach ($kelasList as $kelas)
                    <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="filterTahunAjar">Tahun Ajar</label>
            <select id="filterTahunAjar" class="form-control">
                <option value="">Pilih Tahun Ajar</option>
                @foreach ($tahunAjarList as $tahun)
                    <option value="{{ $tahun->id_tahun_ajar }}">{{ $tahun->tahun_ajar }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="filterBulan">Bulan</label>
            <select id="filterBulan" class="form-control">
                <option value="">Pilih Bulan</option>
                @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $bulan)
                    <option value="{{ $bulan }}">{{ $bulan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="filterMinggu">Minggu</label>
            <select id="filterMinggu" class="form-control">
                <option value="">Pilih Minggu</option>
                @foreach (['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5'] as $minggu)
                    <option value="{{ $minggu }}">{{ $minggu }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 text-right">
            <button id="resetFilters" class="btn btn-secondary">Reset</button>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered" id="absensiTable">
            <thead>
                <tr align="center">
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Santri</th>
                    <th>Bulan</th>
                    <th>Minggu</th>
                    <th>Hadir</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Alpha</th>
                    <th>Kelas</th>
                    <th>Tahun Ajar</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
        </table>
    </div>

@endsection

@section('modal')
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteAbsensiModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="deleteForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Hapus Absensi</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus data absensi ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            let table = $('#absensiTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.absensi.data') }}",
                    type: "GET",
                    data: function(d) {
                        // Kirim nilai filter ke server
                        d.kelas = $('#filterKelas').val();
                        d.tahun_ajar = $('#filterTahunAjar').val();
                        d.bulan = $('#filterBulan').val();
                        d.minggu = $('#filterMinggu').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, // Kolom nomor
                    {
                        data: 'nis',
                        name: 'nis'
                    }, // Kolom NIS
                    {
                        data: 'nama_santri',
                        name: 'nama_santri'
                    }, // Kolom Nama Santri
                    {
                        data: 'bulan',
                        name: 'bulan'
                    }, // Kolom Bulan
                    {
                        data: 'minggu_per_bulan',
                        name: 'minggu_per_bulan'
                    }, // Kolom Minggu
                    {
                        data: 'jumlah_hadir',
                        name: 'jumlah_hadir'
                    }, // Kolom Hadir
                    {
                        data: 'jumlah_izin',
                        name: 'jumlah_izin'
                    }, // Kolom Izin
                    {
                        data: 'jumlah_sakit',
                        name: 'jumlah_sakit'
                    }, // Kolom Sakit
                    {
                        data: 'jumlah_alpha',
                        name: 'jumlah_alpha'
                    }, // Kolom Alpha
                    {
                        data: 'kelas',
                        name: 'kelas'
                    }, // Kolom Kelas
                    {
                        data: 'tahun_ajar',
                        name: 'tahun_ajar'
                    }, // Kolom Tahun Ajar
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    } // Kolom Action
                ],
                order: [
                    [0, 'asc']
                ], // Default pengurutan berdasarkan kolom pertama (No)
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json" // Bahasa Indonesia
                }
            });

            // Event listener untuk filter dropdown
            $('#filterKelas, #filterTahunAjar, #filterBulan, #filterMinggu').change(function() {
                table.draw(); // Memanggil ulang DataTable dengan filter baru
            });

            // Reset filter
            $('#resetFilters').click(function() {
                $('#filterKelas, #filterTahunAjar, #filterBulan, #filterMinggu').val('');
                table.draw(); // Memanggil ulang DataTable tanpa filter
            });

            // Fungsi untuk menghapus data
            function deleteData(id) {
                let url = '{{ route('admin.absensi.destroy', ':id') }}';
                url = url.replace(':id', id);
                $("#deleteForm").attr('action', url);
                $('#deleteAbsensiModal').modal('show');
            }

            // Event listener untuk tombol hapus
            $(document).on('click', '.btn-danger', function() {
                let id = $(this).data('id');
                deleteData(id);
            });
        });
    </script>
@endsection
