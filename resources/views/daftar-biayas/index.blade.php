@extends('layouts.home')
@section('title_page', 'Daftar Biaya')

@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-12">
                <a href="{{ route('daftar-biayas.create') }}" class="btn btn-primary">Tambah Biaya</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered" id="daftar-biaya-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kategori Biaya</th>
                                    <th>Status</th>
                                    <th>Nominal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#daftar-biaya-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ route('daftar-biayas.data') }}',
                    type: 'GET'
                },
                columns: [{
                        data: null, // gunakan null agar bisa diisi manual lewat callback
                        name: 'no',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_kategori',
                        name: 'nama_kategori'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'nominal',
                        name: 'nominal',
                        render: function(data) {
                            // Format nominal to Rupiah
                            return 'Rp ' + data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',


                    }
                ],
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                },
                scrollX: true, // Aktifkan scroll horizontal jika perlu
                autoWidth: false // Nonaktifkan auto width

            });
        });
    </script>
@endsection
