@extends('layouts.home')

@section('title_page', 'Penugasan Wali Kelas')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Buat Wali Kelas</h1>

        <div class="mb-4">
            <select id="tahunAjar" class="form-control">
                <option value="" disabled selected>Pilih Tahun Ajar</option>
                @foreach ($tahunAjar as $tahun)
                    <option value="{{ $tahun->id_tahun_ajar }}">{{ $tahun->tahun_ajar }}</option>
                @endforeach
            </select>
        </div>

        <table id="waliTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelas</th>
                    <th>Ustadz</th>
                </tr>
            </thead>
        </table>

        <div class="mt-4 text-center">
            <button id="saveButton" class="btn btn-primary">Simpan</button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('ustadz.penugasan.index') }}">Kembali</a>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let table;

            $('#tahunAjar').change(function() {
                let tahunAjarId = $(this).val();

                if (tahunAjarId) {
                    if (table) {
                        table.destroy();
                    }

                    table = $('#waliTable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('ustadz.penugasan.mustahiq.getKelas') }}",
                            type: 'GET',
                            data: function(d) {
                                d.id_tahun_ajar = tahunAjarId;
                            }
                        },
                        columns: [{
                                data: null,
                                orderable: false,
                                searchable: false,
                                render: (data, type, row, meta) => meta.row + 1
                            },
                            {
                                data: 'nama_kelas',
                                name: 'nama_kelas'
                            },
                            {
                                data: 'ustadz_dropdown',
                                name: 'ustadz_dropdown',
                                orderable: false,
                                searchable: false
                            },
                        ]
                    });
                }
            });

            $('#saveButton').click(function() {
                let penugasanData = [];
                $('.ustadz-dropdown').each(function() {
                    let kelasId = $(this).data('kelas-id');
                    let ustadzId = $(this).val();
                    console.log(kelasId, ustadzId);
                    penugasanData.push({
                        kelas_id: kelasId,
                        ustadz_id: ustadzId
                    });
                });

                if (penugasanData.length > 0) {
                    $.ajax({
                        url: "{{ route('ustadz.penugasan.mustahiq.store') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id_tahun_ajar: $('#tahunAjar').val(),
                            penugasan: penugasanData
                        },
                        success: function(response) {
                            alert(response.message);
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
                    alert('Pilih ustadz untuk setiap kelas!');
                }
            });
        });
    </script>
@endsection
