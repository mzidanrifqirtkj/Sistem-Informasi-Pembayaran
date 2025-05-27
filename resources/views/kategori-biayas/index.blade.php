@extends('layouts.home')
@section('title_page', 'Kategori Biaya')
@section('content')
    <div class="container mt-1">
        <a href="{{ route('kategori-biayas.create') }}" class="btn btn-primary mb-3">+ Tambah Kategori</a>

        <table class="table table-bordered table-striped" id="kategoriTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kategoriBiayas as $index => $kategori)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ ucfirst($kategori->nama_kategori) }}</td>
                        <td>{{ ucfirst($kategori->status) }}</td>
                        <td>
                            <a href="{{ route('kategori-biayas.edit', $kategori->id_kategori_biaya) }}"
                                class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('kategori-biayas.destroy', $kategori->id_kategori_biaya) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#kategoriTable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Data kosong",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });
    </script>
@endsection
