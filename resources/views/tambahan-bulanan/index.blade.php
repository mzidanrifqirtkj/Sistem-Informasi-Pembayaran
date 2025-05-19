@extends('layouts.home')
@section('title_page', 'Biaya Tambahan Bulanan')
@section('content')
    <div class="row">
        <div class="col-md-2 mb-3">
            {{-- @if (auth()->user()->role == 'Administrator') --}}
            <a href="{{ route('tambahan_bulanan.create') }}" class="btn btn-primary">Tambah Item</a><br><br>
            {{-- @endif --}}
        </div>
    </div>
    <div class="row g-4">
        @foreach ($itemTambahan as $item)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white text-center">
                        <h5 class="mb-0">{{ $item->nama_item }}</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-success mb-2">Rp. {{ number_format($item->nominal, 2, ',', '.') }}</h3>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('tambahan_bulanan.edit', $item->id_tambahan_bulanan) }}"
                                class="btn btn-sm btn-info">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteItem('{{ $item->id_tambahan_bulanan }}')"
                                data-toggle="modal" data-target="#deleteSantriModal">
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
    <div class="modal fade" id="deleteItemModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteFormItem" method="post">
                @method('DELETE')
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter">Hapus Item Biaya</h4>
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
@endsection


@section('script')
    <script>
        // Fungsi untuk tambahan_bulanan
        function deleteItem(id) {
            let url = '{{ route('tambahan_bulanan.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteFormItem").attr('action', url);
            $("#deleteItemModal").modal('show');
        }

        function formSubmitBulanan() {
            $("#deleteItemModal").submit();
        }
    </script>
@endsection
