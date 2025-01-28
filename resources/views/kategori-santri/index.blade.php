@extends('layouts.home')
@section('title_page','Biaya Pembayaran')
@section('content')

    <h2 class="text-center my-4">Biaya Bulanan</h2>
    <div class="row">
        <div class="col-md-2 mb-3">
            {{-- @if (auth()->user()->role == 'Administrator') --}}
            <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary">Tambah Kategori Bulanan</a><br><br>
            {{-- @endif --}}
        </div>
    </div>
    <div class="row g-4">
        @foreach ($kategoriSantri as $kategori)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white text-center">
                        <h5 class="mb-0">{{ $kategori->nama_kategori }}</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-success mb-2">Rp. {{ number_format($kategori->nominal_syahriyah, 2, ',', '.') }}</h3>
                        <p class="text-muted">/bulan</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('admin.kategori.edit', $kategori->id_kategori_santri) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteKategoriSantri('{{ $kategori->id_kategori_santri }}')" data-toggle="modal" data-target="#deleteSantriModal">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection


@section('modal')
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteKategoriModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteFormKategori" method="post">
                @method('DELETE')
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter">Hapus Kategori Bulanan</h4>
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
    function deleteKategoriSantri(id) {
        let url = '{{ route("admin.kategori.destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#deleteFormKategori").attr('action', url);
        $("#deleteKategoriModal").modal('show');
    }
    function formSubmitKategori() {
        $("#deleteFormKategori").submit();
    }
</script>
@endsection

