@extends('layouts.home')
@section('title_page','Data Santri')
@section('content')

    @if (Session::has('alert'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ Session('alert') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-2">
            <a href="{{ route('admin.santri.create') }}" class="btn btn-primary">Tambah Santri</a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.santri.importForm') }}" class="btn btn-primary">Import Santri</a>
        </div>
        <div class="col-md-4 mb-3">
            <form action="#" class="flex-sm">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Search" value="{{ Request::get('keyword') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary mr-2 rounded-right" type="submit"><i class="fas fa-search"></i></button>
                        <button onclick="window.location.href='{{ route('admin.santri.index') }}'" type="button" class="btn btn-md btn-secondary rounded"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr align="center">
                    {{-- <th width="5%">No</th> --}}
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. HP</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($santris as $santri => $result)
                    <tr>
                        {{-- <td>{{ }}</td> --}}
                        <td>{{ $result->nis }}</td>
                        <td><a href="{{ route('admin.santri.show', $result->id_santri) }}">{{ $result->nama_santri }}</a></td>
                        <td>{{ $result->alamat }}</td>
                        <td>{{ $result->no_hp }}</td>
                        <td align="center">
                            {{-- @if (Auth::user()->role == 'Pengurus') --}}
                            {{-- <a href="{{ route('admin.santri.edit', $result->id_santri) }}" type="button" class="btn btn-sm btn-info"><i class="fas fa-pen"></i></a> --}}
                        {{-- @else                                 --}}
                            <a href="{{ route('admin.santri.edit', $result->id_santri) }}" type="button" class="btn btn-sm btn-info"><i class="fas fa-pen"></i></a>
                            <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger" onclick="deleteData('{{ $result->id_santri }}')" data-toggle="modal" data-target="#deleteSantriModal"><i class="fas fa-trash"></i></a>
                            {{-- @endif --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-2 float-left">
        <span class="ml-3">Jumlah Santri Aktif: <span class="text-primary font-weight-bold">{{ $santris->first()?->total_aktif ?? 0 }}</span></span>
    </div>
    <div class="mt-3 float-right">
        {{ $santris->links('pagination::bootstrap-5') }}
    </div>

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
                        <h4 class="modal-title" id="vcenter">Hapus Santri</h4>
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
            url     = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
        }
        function formSubmit() {
            $("#deleteForm").submit();
        }
    </script>
@endsection
