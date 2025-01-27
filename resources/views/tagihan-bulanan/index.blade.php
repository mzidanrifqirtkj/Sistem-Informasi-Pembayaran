@extends('layouts.home')
@section('title_page','Riwayat Tagihan Syahriah')
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
    <div class="col-md-4">
        <a href="{{ route('admin.tagihan_bulanan.create') }}" class="btn btn-primary">Buat Tagihan Syahriah</a><br><br>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.tagihan_bulanan.createBulkBulanan') }}" class="btn btn-primary">Generate Tagihan Syahriah</a><br><br>
    </div>
    <div class="col-md-4 mb-3">
        <form action="#" class="flex-sm">
            <div class="input-group">
                <select class="form-control select2" name="year" id="year">
                    @for ($year = (int) date('Y'); 1900 <= $year; $year--)
                        <option value="{{ $year }}" @if ($year==$now) selected @endif>
                        {{ $year }}
                        </option>
                        @endfor
                </select>
                <div class="input-group-append">
                    <button class="btn btn-primary mr-2 rounded-right" type="submit"><i class="fas fa-search"></i></button>
                    <button onclick="window.location.href='{{ route('admin.tagihan_bulanan.index') }}'" type="button" class="btn btn-md btn-secondary rounded"><i class="fas fa-sync-alt"></i></button>
                </div>
            </div>
            <br>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-bordered">
        <thead>
            <tr align="center">
                <th colspan="14">{{ $now }}</th>
            </tr>
            <tr align="center">
                {{-- <th widt   h="5%">No</th> --}}
                <th class="w-25">Nama Santri</th>
                <th>Jan</th>
                <th>Feb</th>
                <th>Mar</th>
                <th>Apr</th>
                <th>May</th>
                <th>Jun</th>
                <th>Jul</th>
                <th>Aug</th>
                <th>Sep</th>
                <th>Oct</th>
                <th>Nov</th>
                <th>Dec</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($santris as $santri)
            <tr align="center">
                <td><a href="{{ route('admin.santri.show', $santri) }}" target="_blank">{{ $santri->nama_santri }}</a></td>
                @php
                    // Buat array nama bulan secara berurutan
                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    // Ambil daftar bulan yang sudah ada di tagihan
                    $bulanTagihan = $santri->tagihanBulanan->pluck('bulan')->toArray();
                @endphp
                @foreach ($months as $month)
                <td>
                    <div class="custom-control custom-checkbox" style="display: flex">
                        <input type="checkbox" class="custom-control-input" id="cbx-{{ $loop->index }}" disabled
                            @if (in_array($month, $bulanTagihan)) checked @endif>
                        <label class="custom-control-label" for="cbx-{{ $loop->index }}"></label>
                    </div>
                </td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="14">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-2 float-left">
    <span class="ml-3">Data Keseluruhan: <span class="text-primary font-weight-bold">{{ DB::table('santris')->count() }}</span> Santri</span>
</div>
<div class="mt-3 float-right">
    {{ $santris->links() }}
</div>

<br><br><br>
<div class="container">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4>Riwayat Tagihan</h4>
        </div>
        <div class="col-md-4">
            <form action="#" class="flex-sm">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Search" value="{{ Request::get('keyword') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary mr-2 rounded-right" type="submit"><i class="fas fa-search"></i></button>
                        <button onclick="window.location.href='{{ route('admin.tagihan_bulanan.index') }}'" type="button" class="btn btn-md btn-secondary rounded"><i class="fas fa-sync-alt"></i></button>
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
                    <th>Nama</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Nominal</th>
                    <th>Rincian</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataTagihans as $result)
                <tr>
                    {{-- <td>{{ $syahriah + $dataTagihans->firstitem() }}</td> --}}
                    <td><a href="{{ route('admin.santri.show',$result->santri) }}" target="blank">{{ $result->santri->nama_santri }}</a></td>
                    <td>{{ $result->bulan }}</td>
                    <td>{{ $result->tahun }}</td>
                    <td>{{ $result->nominal }}</td>
                    <td>{{ json_encode($result->rincian) }}</td>
                    {{-- <td>{{ $result->date }}</td> --}}
                    <td align="center">
                        <a href="{{ route('admin.tagihan_bulanan.edit', $result->id_tagihan_bulanan) }}" type="button" class="btn btn-sm btn-warning"><i class="fas fa-print"></i></a>
                        {{-- @if (auth()->user()->role == 'Administrator') --}}
                        <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger" onclick="deleteData('{{ $result->id_tagihan_bulanan }}')" data-toggle="modal" data-target="#deleteSyahriahModal"><i class="fas fa-trash"></i></a>
                        {{-- @endif --}}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-2 float-left">
        <span class="ml-3">Data Keseluruhan: <span class="text-primary font-weight-bold">{{ DB::table('tagihan_bulanans')->count() }}</span> Tagihan syahriah telah terbuat.</span>
    </div>
    <div class="mt-3 float-right">
        {{-- {{ $dataTagihans->links('pagination::bootstrap-5') }}  --}}
    </div>
</div>

@endsection

@section('modal')
<!-- Modal Delete -->
<div class="modal fade" id="deleteSyahriahModal" tabindex="-1" role="dialog">
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
        let url = '{{ route("admin.tagihan_bulanan.destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#deleteForm").attr('action', url);
    }

    function formSubmit() {
        $("#deleteForm").submit();
    }
</script>
@endsection

{{-- <table class="table table-hover table-bordered">
    <thead>
        <tr align="center">
            <th colspan="14">{{ $now }}</th>
        </tr>
        <tr align="center">
            <th class="w-25">Nama Santri</th>
            <th>Jan</th>
            <th>Feb</th>
            <th>Mar</th>
            <th>Apr</th>
            <th>May</th>
            <th>Jun</th>
            <th>Jul</th>
            <th>Aug</th>
            <th>Sep</th>
            <th>Oct</th>
            <th>Nov</th>
            <th>Dec</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($santris as $santri)
            <tr align="center">
                <td><a href="{{ route('admin.santri.show', $santri) }}" target="blank">{{ $santri->nama_santri }}</a></td>
                @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                    <td>
                        <div class="custom-control custom-checkbox" style="display: flex">
                            <input type="checkbox" class="custom-control-input" id="cbx-{{ $santri->id_santri }}-{{ $month }}" disabled
                                @if ($santri->tagihanBulanan->contains(fn($tagihan) => $tagihan->bulan == $month)) checked @endif >
                            <label class="custom-control-label" for="cbx-{{ $santri->id_santri }}-{{ $month }}"></label>
                        </div>
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="14">Tidak ada data.</td>
            </tr>
        @endforelse
    </tbody>
</table> --}}
