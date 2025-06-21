<div class="preview-payment">
    <h6 class="mb-3">Detail Pembayaran</h6>

    <div class="row mb-3">
        <div class="col-md-6">
            <small class="text-muted">Nama Santri:</small><br>
            <strong>{{ $santri->nama_santri }}</strong>
        </div>
        <div class="col-md-6">
            <small class="text-muted">NIS:</small><br>
            <strong>{{ $santri->nis }}</strong>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <small class="text-muted">Nominal Pembayaran:</small><br>
            <h5 class="text-primary mb-0">Rp {{ number_format($nominal_pembayaran, 0, ',', '.') }}</h5>
        </div>
        <div class="col-md-6">
            <small class="text-muted">Total Dialokasikan:</small><br>
            <h5 class="text-success mb-0">Rp {{ number_format($total_allocated, 0, ',', '.') }}</h5>
        </div>
    </div>

    @if ($sisa_pembayaran > 0)
        <div class="alert alert-warning">
            <i class="fas fa-info-circle"></i>
            Terdapat kelebihan pembayaran sebesar <strong>Rp {{ number_format($sisa_pembayaran, 0, ',', '.') }}</strong>
            <br>
            <small>Kelebihan pembayaran akan dicatat dan dapat digunakan untuk pembayaran berikutnya.</small>
        </div>
    @endif

    <h6 class="mb-2">Alokasi Pembayaran:</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th>Sisa Tagihan</th>
                    <th>Dibayar</th>
                    <th>Status Setelah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allocations as $allocation)
                    <tr>
                        <td>
                            @if ($allocation['type'] == 'bulanan')
                                <span class="badge badge-primary">Bulanan</span>
                            @else
                                <span class="badge badge-success">Terjadwal</span>
                            @endif
                        </td>
                        <td>
                            @if ($allocation['type'] == 'bulanan')
                                {{ $allocation['tagihan']->bulan }} {{ $allocation['tagihan']->tahun }}
                            @else
                                {{ $allocation['tagihan']->daftarBiaya->nama_biaya ?? 'Tagihan Terjadwal' }}
                            @endif
                        </td>
                        <td class="text-right">
                            Rp {{ number_format($allocation['tagihan']->sisa_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="text-right font-weight-bold">
                            Rp {{ number_format($allocation['allocated_amount'], 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if ($allocation['status_after'] == 'lunas')
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Lunas
                                </span>
                            @elseif($allocation['status_after'] == 'dibayar_sebagian')
                                <span class="badge badge-warning">Sebagian</span>
                            @else
                                <span class="badge badge-danger">Belum Lunas</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <th colspan="3" class="text-right">Total:</th>
                    <th class="text-right">Rp {{ number_format($total_allocated, 0, ',', '.') }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="alert alert-info mb-0">
        <i class="fas fa-info-circle"></i>
        Pastikan data di atas sudah benar sebelum mengkonfirmasi pembayaran.
    </div>
</div>
