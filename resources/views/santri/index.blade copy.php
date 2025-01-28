@extends('layouts.home')

@section('title_page', 'Data Santri')

@section('content')

<div class="row mb-3">
    <div class="col-md-2">
        <a href="{{ route('admin.santri.create') }}" class="btn btn-primary">Tambah Santri</a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.santri.importForm') }}" class="btn btn-primary">Import Santri</a>
    </div>
    {{-- <div class="col-md-4 mb-3">
        <form action="#" class="d-flex">
            <div class="input-group">
                <input type="text" name="keyword" class="form-control" placeholder="Search" value="{{ Request::get('keyword') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    <button onclick="window.location.href='{{ route('admin.santri.index') }}'" type="button" class="btn btn-md btn-secondary"><i class="fas fa-sync-alt"></i></button>
                </div>
            </div>
        </form>
    </div> --}}
</div>

<div class="table-responsive">
    <table id="santriTable" class="table table-hover table-bordered">
        <thead>
            <tr align="center">
                <th>NIS</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No. HP</th>
                <th width="13%">Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be populated via AJAX -->
        </tbody>
    </table>
</div>

{{-- <div class="mt-3 float-left">
    <span class="ml-3">Jumlah Santri Aktif: <span class="text-primary font-weight-bold">{{ $santris->first()?->total_aktif ?? 0 }}</span></span>
</div>

<div class="mt-3 float-right">
    {{ $santris->links('pagination::bootstrap-5') }}
</div> --}}

@endsection

@section('modal')
<!-- Modal Delete -->
<div class="modal fade" id="deleteSantriModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="javascript:void(0)" id="deleteForm" method="post">
            @method('DELETE')
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Hapus Santri</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" onclick="formSubmit()" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    // Fungsi untuk menghapus data santri
    function deleteData(id) {
        let url = '{{ route("admin.santri.destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#deleteForm").attr('action', url);
    }

    // Fungsi untuk submit form hapus
    function formSubmit() {
        $("#deleteForm").submit();
    }

    // Inisialisasi DataTable dengan AJAX
    $(document).ready(function() {
        $('#santriTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.santri.index") }}',
                type: 'GET'
            },
            columns: [
                { data: 'nis', name: 'nis' },
                { data: 'nama_santri', name: 'nama_santri' },
                { data: 'alamat', name: 'alamat' },
                { data: 'no_hp', name: 'no_hp' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
