@extends('layouts.home')

@section('title_page', 'Penugasan Ustadz')

@section('content')

<div class="container">
    <h1 class="text-center mb-4">Buat Penugasan Ustadz</h1>

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
                <th>Ustadz</th>
            </tr>
        </thead>
    </table>

    <div class="mt-4 text-center">
        <button id="saveButton" class="btn btn-primary">Simpan</button>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('admin.ustadz.penugasan.index') }}">Kembali</a>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        let table;

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
                        url: "{{ route('admin.ustadz.penugasan.qori.getPelajaran') }}",
                        type: 'GET',
                        data: function(d) {
                            d.id_tahun_ajar = $('#tahunAjar').val();
                            d.id_kelas = $('#kelas').val();
                            console.log("Data yang dikirim ke server:", d);
                        }
                    },
                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + 1; // Nomor urut
                            }
                        },
                        {
                            data: 'mata_pelajaran.nama_mapel',
                            name: 'mata_pelajaran.nama_mapel'
                        },
                        {
                            data: 'ustadz_dropdown',
                            name: 'ustadz_dropdown',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

            }
        });

        $('#saveButton').click(function() {
            let penugasanData = [];

            // Ambil data penugasan dari dropdown mapel
            $('.ustadz-dropdown').each(function() {
                let mapelKelasId = $(this).data('mapel-kelas-id');
                let ustadzId = $(this).val();

                penugasanData.push({
                    mapel_kelas_id: mapelKelasId,
                    ustadz_id: ustadzId,
                });
            });
            if (penugasanData.length > 0) {
                $.ajax({
                    url: "{{ route('admin.ustadz.penugasan.qori.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        penugasan: penugasanData,
                    },
                    success: function(response) {
                        alert('Penugasan berhasil disimpan!');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;
                        let errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                        if (response) {
                            if (response.message) {
                                errorMsg += ' ' + response.message;
                            } else if (response.messages) {
                                // Jika response.messages adalah array, gabungkan pesan-pesan error
                                errorMsg += ' ' + response.messages.join(' ');
                            }
                        }
                        alert(errorMsg);
                        console.error(xhr.responseText);
                    }
                });
            } else {
                alert('Pilih ustadz untuk setiap mata pelajaran!');
            }
        });


    });
</script>
@endsection
