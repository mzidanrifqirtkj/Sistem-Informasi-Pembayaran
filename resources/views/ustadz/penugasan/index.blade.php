@extends('layouts.home')
@section('title_page','Daftar Ustadz')
@section('content')


<div class="row">
    <div class="col-md-2">
        <a href="{{ route('admin.ustadz.penugasan.create') }}" class="btn btn-primary">Tambah Penugasan</a><br><br>
    </div>

    <div class="col-md-8 mb-3">
        <form action="#" class="flex-sm">
            <div class="input-group">
                <input type="text" name="keyword" class="form-control" placeholder="Search" value="{{ Request::get('keyword') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary mr-2 rounded-right" type="submit"><i class="fas fa-search"></i></button>
                    {{-- <button onclick="window.location.href='{{ route('admin.santri.index') }}'" type="button" class="btn btn-md btn-secondary rounded"><i class="fas fa-sync-alt"></i></button> --}}
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>Ustadz</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penugasans as $penugasan)
            <tr>
                <td>{{ $penugasan->ustadz->nama_santri }}</td>
                <td>{{ $penugasan->mataPelajaran->nama_mapel }}</td>
                <td>{{ $penugasan->kelas->nama_kelas ?? '-' }}</td>
                <td>
                    <a href="{{ route('penugasan.edit', $penugasan->id_penugasan) }}">Edit</a>
                    <form action="{{ route('penugasan.destroy', $penugasan->id_penugasan) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{--
<div class="mt-3 float-right">
    {{ $penugasans->links('pagination::bootstrap-5') }}
</div> --}}

@endsection

@section('modal')
<!-- Modal Delete -->
<div class="modal fade" id="deleteKelasModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="javascript:void(0)" id="deleteForm" method="post">
            @method('DELETE')
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="vcenter">Hapus Kelas</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah anda yakin?</p>
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

    function deleteData(id) {
        let url = '{{ route("admin.santri.destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#deleteForm").attr('action', url);
    }

    function formSubmit() {
        $("#deleteForm").submit();
    }
</script>
@endsection
