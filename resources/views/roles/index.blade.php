@extends('layouts.home')
@section('title_page', 'Roles & Permissions')
@section('content')
    <div class="row">
        <div class="col-md-4">
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Tambah Role</a>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered" id="rolesTable">
            <thead>
                <tr align="center">
                    <th width="5%">No</th>
                    <th>Nama Role</th>
                    <th>Permissions</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="mt-2 float-left">
        <span class="ml-3">Data Keseluruhan: <span
                class="text-primary font-weight-bold">{{ DB::table('roles')->count() }}</span> role.</span>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="deleteRoleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="deleteForm" method="post">
                @method('DELETE') @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Hapus Role</h4>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin ingin menghapus role ini?</p>
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
        $(document).ready(function() {
            $('#rolesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('roles.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'permissions',
                        name: 'permissions'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        function deleteData(id) {
            let url = '{{ route('roles.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $("#deleteRoleModal").modal('show');
        }

        function formSubmit() {
            $("#deleteForm").submit();
        }
    </script>
@endsection
