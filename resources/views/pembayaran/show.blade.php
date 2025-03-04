@extends('layouts.home')
@section('title_page', 'Pembayaran Santri')
@section('content')

    @if (Session::has('alert'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ Session('alert') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="container">
        <h1>Tagihan untuk {{ $santri->nama_santri }}</h1>

        <h4>Tagihan Bulanan</h4>
        <table id="example" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($santri->tagihanBulanan as $tagihan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $tagihan->bulan }}</td>
                        <td>{{ $tagihan->tahun }}</td>
                        <td>{{ $tagihan->nominal }}</td>
                        <td>
                            <span
                                class="status-checkbox {{ $tagihan->status == 'lunas' ? 'text-primary' : 'text-danger' }}">
                                {{ $tagihan->status == 'lunas' ? '✔' : '✖' }}
                            </span>
                        </td>

                        {{-- <td>{{ $tagihan->status }}</td> --}}
                        @if ($tagihan->status === 'lunas')
                            <td>
                                <a href="{{ route('tagihan_bulanan.edit', $tagihan->id_tagihan_bulanan) }}" type="button"
                                    class="btn btn-sm btn-warning"><i class="fas fa-print"></i></a>
                                {{-- @if (Auth::user()->role == 'Administrator')                             --}}
                                <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger"
                                    onclick="deleteDataBulanan('{{ $tagihan->id_tagihan_bulanan }}')" data-toggle="modal"
                                    data-target="#deleteModal"><i class="fas fa-trash"></i></a>
                            </td>
                        @else
                            <td>
                                <form action="{{ route('pembayaran.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="santri_id" value="{{ $santri->id_santri }}">
                                    <input type="hidden" name="jenis_tagihan" value="bulanan">
                                    <input type="hidden" name="tagihan_id" value="{{ $tagihan->id_tagihan_bulanan }}">
                                    <input type="number" name="nominal" placeholder="Masukkan Nominal" required>
                                    <button type="submit" class="btn btn-success">Bayar</button>
                                </form>
                            </td>
                        @endif
                @endforeach
            </tbody>
        </table>

        <h4>Tagihan Terjadwal</h4>
        <table id="example" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Tagihan</th>
                    <th>Tahun</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($santri->tagihanTerjadwal as $tagihan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $tagihan->biayaTerjadwal->nama_biaya }}</td>
                        <td>{{ $tagihan->tahun }}</td>
                        <td>{{ $tagihan->nominal }}</td>
                        <td>
                            <span
                                class="status-checkbox {{ $tagihan->status == 'lunas' ? 'text-primary' : 'text-danger' }}">
                                {{ $tagihan->status == 'lunas' ? '✔' : '✖' }}
                            </span>
                        </td>
                        @if ($tagihan->status === 'lunas')
                            <td>
                                <a href="{{ route('tagihan_terjadwal.edit', $tagihan->id_tagihan_terjadwal) }}"
                                    type="button" class="btn btn-sm btn-warning"><i class="fas fa-print"></i></a>
                                {{-- @if (Auth::user()->role == 'Administrator')                             --}}
                                <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger"
                                    onclick="deleteDataTerjadwal('{{ $tagihan->id_tagihan_terjadwal }}')"
                                    data-toggle="modal" data-target="#deleteModal"><i class="fas fa-trash"></i></a>
                            </td>
                        @else
                            <td>
                                <form action="{{ route('pembayaran.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="santri_id" value="{{ $santri->id_santri }}">
                                    <input type="hidden" name="jenis_tagihan" value="terjadwal">
                                    <input type="hidden" name="tagihan_id" value="{{ $tagihan->id_tagihan_terjadwal }}">
                                    <input type="number" name="nominal" placeholder="Masukkan Nominal" required>
                                    <button type="submit" class="btn btn-success">Bayar</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection

@section('modal')
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteTagihanBulananModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteFormBulanan" method="post">
                @method('DELETE')
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter">Hapus Tagihan Bulanan</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" onclick="formSubmitBulanan()" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteTagihanTerjadwalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteFormTerjadwal" method="post">
                @method('DELETE')
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter">Hapus Tagihan Terjadwal</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" onclick="formSubmitTerjadwal()" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function deleteDataBulanan(id) {
            let url = '{{ route('tagihan_bulanan.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteFormBulanan").attr('action', url);
            $("#deleteTagihanBulananModal").modal('show');
        }

        function deleteDataTerjadwal(id) {
            let url = '{{ route('tagihan_terjadwal.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteFormTerjadwal").attr('action', url);
            $("#deleteTagihanTerjadwalModal").modal('show');
        }

        function formSubmitBulanan() {
            $("#deleteFormBulanan").submit();
        }

        function formSubmitTerjadwal() {
            $("#deleteFormTerjadwal").submit();
        }
    </script>
@endsection
