@extends('layouts.home')
@section('title_page', 'Data Qori Kelas')

{{-- @section('css_inline')
    <style>
        .toggle-status {
            transition: all 0.3s;
        }

        .toggle-status:hover {
            transform: rotate(180deg);
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }
    </style>
@endsection --}}
@section('content')
    <div class="container">
        <form action="{{ route('qori_kelas.generate') }}" method="POST" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-primary" id="generateBtn">
                <i class="fas fa-sync-alt"></i> Generate Qori dari Data Santri
            </button>
        </form>

        <table class="table" id="qoriTable">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($qoriKelas as $qori)
                    <tr id="row-{{ $qori->id }}">
                        <td>{{ $qori->nis }}</td>
                        <td>{{ $qori->santri->nama_santri ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $qori->status == 'Aktif' ? 'success' : 'danger' }}">
                                {{ ucfirst($qori->status) }}
                            </span>
                        </td>
                        <td>
                            <!-- Form Status -->

                            <form action="{{ route('qori_kelas.toggle-status', $qori->id_qori_kelas) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <input type="hidden" name="status"
                                    value="{{ $qori->status === 'Aktif' ? 'Tidak Aktif' : 'Aktif' }}">
                                <button type="submit"
                                    class="btn btn-sm {{ $qori->status === 'Aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                    onclick="return confirm('Apakah Anda yakin ingin mengubah status?')">
                                    Ubah Status
                                </button>
                            </form>

                            <!-- Form hapus -->

                            <form action="{{ route('qori_kelas.destroy', $qori->id_qori_kelas) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>

                        </td>
                        {{-- <td>
                            <button class="btn btn-sm btn-outline-warning toggle-status" data-id="{{ $qori->id }}"
                                data-status="{{ $qori->status }}">
                                Ubah Status
                            </button>

                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $qori->id }}"
                                data-name="{{ $qori->santri->nama_santri ?? $qori->nis }}">
                                Hapus
                            </button>
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        console.log('Script siap!');

        $('#qoriTable').DataTable();

        $(document).on('click', '.toggle-status', function() {
            const button = $(this);
            const id = button.data('id');
            const currentStatus = button.data('status');
            const newStatus = currentStatus === 'aktif' ? 'non-aktif' : 'aktif';

            Swal.fire({
                title: 'Ubah Status?',
                html: `Apakah Anda yakin ingin mengubah status menjadi <b>${newStatus}</b>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const toggleUrl =
                        "{{ route('qori_kelas.toggle-status', ['id' => 'ID_PLACEHOLDER']) }}"
                        .replace('ID_PLACEHOLDER', id);

                    $.ajax({
                        url: toggleUrl,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            status: newStatus
                        },
                        success: function(response) {
                            if (response.success) {
                                button.data('status', newStatus);
                                const badge = $(`#row-${id} .badge`);
                                badge.removeClass('bg-success bg-danger')
                                    .addClass(newStatus === 'aktif' ? 'bg-success' :
                                        'bg-danger')
                                    .text(newStatus.charAt(0).toUpperCase() +
                                        newStatus.slice(1));

                                Swal.fire('Berhasil!', 'Status berhasil diubah.',
                                    'success');
                            }
                        },
                        error: function() {
                            Swal.fire('Gagal!',
                                'Terjadi kesalahan saat mengubah status.',
                                'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.delete-btn', function() {
            const button = $(this);
            const id = button.data('id');
            const name = button.data('name');

            Swal.fire({
                title: 'Hapus Data?',
                html: `Apakah Anda yakin ingin menghapus data <b>${name}</b>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const deleteUrl =
                        "{{ route('qori_kelas.destroy', ['id' => 'ID_PLACEHOLDER']) }}"
                        .replace('ID_PLACEHOLDER', id);

                    $.ajax({
                        url: '/qori_kelas/' + id,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },
                        success: function(response) {
                            if (response.success) {
                                $(`#row-${id}`).remove();
                                Swal.fire('Terhapus!', 'Data berhasil dihapus.',
                                    'success');
                            }
                        },
                        error: function() {
                            Swal.fire('Gagal!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error');
                        }
                    });
                }
            });
        });
    });
</script> --}}
