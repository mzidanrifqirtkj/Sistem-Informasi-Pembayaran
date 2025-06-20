@extends('layouts.home')
@section('title_page', 'Data Kelas')
@section('content')


    <div class="row">
        <div class="col-md-2">
            <a href="{{ route('tahun_ajar.create') }}" class="btn btn-primary">Tambah Tahun Ajar</a><br><br>
        </div>

        <div class="col-md-8 mb-3">
            <form action="#" class="flex-sm">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Search"
                        value="{{ Request::get('keyword') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary mr-2 rounded-right" type="submit"><i
                                class="fas fa-search"></i></button>
                        <button onclick="window.location.href='{{ route('tahun_ajar.index') }}'" type="button"
                            class="btn btn-md btn-secondary rounded"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr align="center">
                    <th width="5%">No</th>
                    <th>Tahun Ajar</th>
                    <th>Status</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tahun_ajar as $thn_ajar)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $thn_ajar->tahun_ajar }}</td>
                        @if ($thn_ajar->status == 'aktif')
                            <td><span class="badge badge-success">aktif</span></td>
                        @else
                            <td><span class="badge badge-danger">Tidak Aktif</span></td>
                        @endif

                        {{-- <td>{{ $thn_ajar->status }}</td> --}}
                        <td align="center">
                            {{-- @if (Auth::tahun_ajar()->role == 'Pengurus')
                            <small class="text-warning">No Action</small>
                        @else --}}
                            <a href="{{ route('tahun_ajar.edit', $thn_ajar) }}" type="button"
                                class="btn btn-sm btn-info"><i class="fas fa-pen"></i></a>
                            <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger"
                                onclick="deleteData('{{ $thn_ajar->id_tahun_ajar }}')" data-toggle="modal"
                                data-target="#deleteKelasModal"><i class="fas fa-trash"></i></a>
                            {{-- @endif --}}
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="4">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- <div class="mt-3 float-right">
    {{ $tahun_ajar->links('pagination::bootstrap-5') }}
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
            let url = '{{ route('tahun_ajar.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
        }

        function formSubmit() {
            $("#deleteForm").submit();
        }
    </script>
@endsection
