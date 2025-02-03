@extends('layouts.home')
@section('title_page','Data Santri')
@section('content')

<div class="row">
    <div class="col-md-4 d-flex justify-content-between">
        <a href="{{ route('admin.santri.create') }}" class="btn btn-primary">Tambah Santri</a>
        <a href="{{ route('admin.santri.importForm') }}" class="btn btn-primary">Import Santri</a>
    </div>
</div>

<div class="table-responsive mt-3">
    <table class="table table-hover table-bordered" id="santriTable">
        <thead>
            <tr align="center">
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No. HP</th>
                <th width="13%">Action</th>
            </tr>
        </thead>
    </table>
</div>

@endsection

@section('modal')
<!-- Modal Delete -->
<div class="modal fade" id="deleteSantriModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="deleteForm" method="post">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Hapus Santri</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus santri ini?</p>
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
    $('#santriTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.santri.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nis', name: 'nis' },
            { data: 'nama_santri', name: 'nama_santri', render: function(data, type, row) {
                return '<a href="' + '{{ route("admin.santri.show", ":id") }}'.replace(":id", row.id_santri) + '">' + data + '</a>';
            }},
            { data: 'alamat', name: 'alamat' },
            { data: 'no_hp', name: 'no_hp' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});

function deleteData(id) {
    let url = '{{ route("admin.santri.destroy", ":id") }}';
    url = url.replace(':id', id);
    $("#deleteForm").attr('action', url);
    $('#deleteSantriModal').modal('show');
}
</script>
@endsection
