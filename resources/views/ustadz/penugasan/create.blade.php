@extends('layouts.home')

@section('title_page', 'Penugasan Ustadz')

@section('content')

<div class="container">
    <h1 class="text-center mb-4">Penugasan Guru</h1>

    <div class="mb-4">
        <select id="tahunAjar" class="form-control">
            <option value="" disabled selected>Pilih Tahun Ajar</option>
            @foreach($tahunAjar as $tahun)
                <option value="{{ $tahun->id_tahun_ajar }}">{{ $tahun->tahun_ajar }}</option>
            @endforeach
        </select>

        <select id="kelas" class="form-control mt-2">
            <option value="" disabled selected>Pilih Kelas</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
    </div>

    <table id="penugasanTable" class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
            </tr>
        </thead>
    </table>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        let table;

        // Kirim data ustadz ke JS
        // var ustadzs = @json($ustadzs); // Mengirim data guru dari server ke JS

        $('#tahunAjar, #kelas').change(function() {
            let tahunAjarId = $('#tahunAjar').val();
            let kelasId = $('#kelas').val();

            if (tahunAjarId && kelasId) {
                if (table) {
                    table.destroy(); // Hancurkan instance DataTable yang lama
                }

                table = $('#penugasanTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('penugasan.getPelajaran') }}",
                        type: 'GET',
                        data: {
                            id_tahun_ajar: tahunAjarId,
                            id_kelas: kelasId
                        }
                    },
                    columns: [
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row, meta) {
                                return meta.row + 1; // Menampilkan nomor urut
                            }
                        },
                        { data: 'nama_mapel', name: 'nama_mapel' },
                        { data: 'guru', name: 'guru', orderable: false, searchable: false }
                    ],
                    "drawCallback": function(settings) {
                        // Set dropdown untuk memilih guru di setiap baris
                        $('.guru-dropdown').each(function() {
                            let select = $(this);
                            let mapelId = select.data('id');
                            // Menambahkan pilihan guru ke dropdown
                            // ustadzs.forEach(function(guru) {
                            //     select.append(new Option(guru.nama_santri, guru.id_santri));
                            // });
                        });
                    }
                });
            }
        });
    });
</script>
@endsection
