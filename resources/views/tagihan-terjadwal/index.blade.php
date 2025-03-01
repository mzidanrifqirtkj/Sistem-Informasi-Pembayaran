@extends('layouts.home')
@section('title_page', 'Tagihan Terjadwal')
@section('content')

    <div class="row">
        @can('create_tagihan_terjadwal')
            <div class="col-md-2">
                <a href="{{ route('admin.tagihan_terjadwal.create') }}" class="btn btn-primary">Buat Tagihan</a><br><br>
            </div>
        @endcan
        @can('bulk_generate_tagihan_terjadwal')
            <div class="col-md-2">
                <a href="{{ route('admin.tagihan_terjadwal.createBulkTerjadwal') }}" class="btn btn-primary">Generate
                    Tagihan</a><br><br>
            </div>
        @endcan
        <div class="col-md-8 mb-3">
            <form action="#" class="flex-sm">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Search"
                        value="{{ Request::get('keyword') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary mr-2 rounded-right" type="submit"><i
                                class="fas fa-search"></i></button>
                        <button onclick="window.location.href='{{ route('admin.tagihan_terjadwal.index') }}'" type="button"
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
                    <th>Nama Santri</th>
                    <th>Nama Tagihan</th>
                    <th>Tahun</th>
                    <th>Nominal</th>
                    <th>Rincian</th>
                    @can('edit_tagihan_terjadwal')
                        <th width="13%">Action</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse ($tagihanTerjadwals as $result => $tagihan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><a href="{{ route('admin.santri.show', $tagihan->santri) }}"
                                target="blank">{{ $tagihan->santri->nama_santri }}</a></td>
                        <td><a href="{{ route('admin.biaya_terjadwal.index') }}"
                                target="blank">{{ $tagihan->biayaTerjadwal->nama_biaya }}</a></td>
                        <td>{{ $tagihan->tahun }}</td>
                        <td>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                        <td>

                            @if (is_array($tagihan->rincian) && !empty($tagihan->rincian))
                                <ul class="list-group">
                                    @foreach ($tagihan->rincian as $item)
                                        <li class="list-group-item">
                                            {{ $item['keterangan'] ?? $item }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="badge bg-secondary">Tidak ada rincian</span>
                            @endif
                        </td>
                        @can('edit_tagihan_terjadwal')
                            <td align="center">
                                <a href="{{ route('admin.tagihan_terjadwal.edit', $tagihan->id_tagihan_terjadwal) }}"
                                    type="button" class="btn btn-sm btn-info"><i class="fas fa-pen"></i></a>
                                {{-- @if (Auth::user()->role == 'Administrator')                             --}}
                                <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger"
                                    onclick="deleteData('{{ $tagihan->id_tagihan_terjadwal }}')" data-toggle="modal"
                                    data-target="#deleteModal"><i class="fas fa-trash"></i></a>
                                {{-- @endif --}}
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-2 float-left">
        {{-- <span class="ml-3">Data Keseluruhan: <span class="text-primary font-weight-bold">{{ DB::table('tagihan_terjadwals')->count() }}</span> Pembayaran pendaftar baru telah terdaftar.</span> --}}
    </div>
    <div class="mt-3 float-right">
        {{ $tagihanTerjadwals->links('pagination::bootstrap-5') }}
    </div>

@endsection

@section('modal')
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteForm" method="post">
                @method('DELETE')
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter">Hapus Data</h4>
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
            let url = '{{ route('admin.tagihan_terjadwal.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
        }

        function formSubmit() {
            $("#deleteForm").submit();
        }
    </script>
@endsection
