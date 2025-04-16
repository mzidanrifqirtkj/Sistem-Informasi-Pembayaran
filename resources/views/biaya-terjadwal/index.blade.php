@extends('layouts.home')
@section('title_page', 'Biaya Pembayaran')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Biaya Terjadwal</h2>
        <a href="{{ route('biaya_terjadwal.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Tambah Biaya
        </a>
    </div>

    <div class="row">
        <!-- Dana Tahunan Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Dana Tahunan</h4>
                    <span class="badge bg-light text-primary">{{ $biayaTerjadwals->where('periode', 'tahunan')->count() }}
                        Item</span>
                </div>
                <div class="card-body p-0">
                    @forelse ($biayaTerjadwals->filter(function($item) { return strtolower($item->periode) === 'tahunan'; }) as $biaya)
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 font-weight-bold">{{ $biaya->nama_biaya }}</h5>

                            </div>
                            <div class="text-right">
                                <h4 class="text-success mb-1">Rp {{ number_format($biaya->nominal, 0, ',', '.') }}</h4>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('biaya_terjadwal.edit', $biaya->id_biaya_terjadwal) }}"
                                        class="btn btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger"
                                        onclick="deleteBiayaTerjadwal('{{ $biaya->id_biaya_terjadwal }}')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>Tidak ada data dana tahunan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Dana Eksidental Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-exclamation-circle mr-2"></i>Dana Eksidental</h4>
                    <span class="badge bg-light text-warning">{{ $biayaTerjadwals->where('periode', 'sekali')->count() }}
                        Item</span>
                </div>
                <div class="card-body p-0">
                    @forelse ($biayaTerjadwals->filter(function($item) { return strtolower($item->periode) === 'sekali'; }) as $biaya)
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 font-weight-bold">{{ $biaya->nama_biaya }}</h5>

                            </div>
                            <div class="text-right">
                                <h4 class="text-success mb-1">Rp {{ number_format($biaya->nominal, 0, ',', '.') }}</h4>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('biaya_terjadwal.edit', $biaya->id_biaya_terjadwal) }}"
                                        class="btn btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger"
                                        onclick="deleteBiayaTerjadwal('{{ $biaya->id_biaya_terjadwal }}')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>Tidak ada data dana eksidental</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteBiayaTerjadwalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteFormTerjadwal" method="post">
                @method('DELETE')
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus biaya ini? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" onclick="formSubmitTerjadwal()" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function deleteBiayaTerjadwal(id) {
            let url = '{{ route('biaya_terjadwal.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteFormTerjadwal").attr('action', url);
            $("#deleteBiayaTerjadwalModal").modal('show');
        }

        function formSubmitTerjadwal() {
            $("#deleteFormTerjadwal").submit();
        }
    </script>
@endsection
