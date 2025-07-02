@extends('layouts.home')
@section('title_page', 'Riwayat Kelas')
@section('content')
    <div class="container">

        @unless (auth()->user()->hasRole('santri'))
            <a href="{{ route('riwayat-kelas.create') }}" class="btn btn-primary mb-3">
                + Tambah Riwayat Kelas
            </a>

            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="filterKelas" class="form-control select2">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $item)
                            <option value="{{ $item->id_kelas }}">{{ $item->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="filterMapel" class="form-control select2">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach ($mapel as $item)
                            <option value="{{ $item->id_mapel }}">{{ $item->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="filterTahun" class="form-control select2">
                        <option value="">-- Pilih Tahun Ajar --</option>
                        @foreach ($tahunAjar as $item)
                            <option value="{{ $item->id_tahun_ajar }}">{{ $item->tahun_ajar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <button id="clearFilter" class="btn btn-secondary w-100">
                        Hapus Filter
                    </button>
                </div>
            </div>
        @endunless

        <table id="riwayatTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Santri</th>
                    <th>Kelas</th>
                    <th>Mapel</th>
                    <th>Tahun Ajar</th>
                    @if (auth()->user()->can('riwayat-kelas.edit') || auth()->user()->can('riwayat-kelas.delete'))
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            const table = $('#riwayatTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('riwayat-kelas.data') }}',
                    data: function(d) {
                        @unless (auth()->user()->hasRole('santri'))
                            d.kelas_id = $('#filterKelas').val();
                            d.mapel_id = $('#filterMapel').val();
                            d.tahun_ajar_id = $('#filterTahun').val();
                        @endunless
                    }
                },
                columns: [{
                        data: 'santri',
                        name: 'santri'
                    },
                    {
                        data: 'kelas',
                        name: 'kelas'
                    },
                    {
                        data: 'mapel',
                        name: 'mapel'
                    },
                    {
                        data: 'tahun_ajar',
                        name: 'tahun_ajar'
                    },
                    @if (auth()->user()->can('riwayat-kelas.edit') || auth()->user()->can('riwayat-kelas.delete'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    @endif
                ]
            });

            @unless (auth()->user()->hasRole('santri'))
                $('#filterKelas, #filterMapel, #filterTahun').change(function() {
                    table.ajax.reload();
                });

                $('#clearFilter').click(function() {
                    $('#filterKelas').val('').trigger('change');
                    $('#filterMapel').val('').trigger('change');
                    $('#filterTahun').val('').trigger('change');
                    table.ajax.reload();
                });
            @endunless
        });
    </script>
@endsection
