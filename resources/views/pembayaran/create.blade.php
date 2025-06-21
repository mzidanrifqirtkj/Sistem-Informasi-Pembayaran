@extends('layouts.home')
@section('title_page', 'Form Pembayaran')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>Pembayaran untuk {{ $santri->nama_santri }}</h1>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Santri Info -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>NIS:</strong> {{ $santri->nis }}
                            </div>
                            <div class="col-md-3">
                                <strong>Nama:</strong> {{ $santri->nama_santri }}
                            </div>
                            <div class="col-md-3">
                                <strong>Kategori:</strong> {{ $santri->kategoriSantri->nama_kategori }}
                            </div>
                            <div class="col-md-3">
                                <strong>Total Tunggakan:</strong>
                                <span class="text-danger font-weight-bold">
                                    Rp {{ number_format($total_tunggakan, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <form id="paymentForm" action="{{ route('pembayaran.store') }}" method="POST">
            @csrf
            <input type="hidden" name="santri_id" value="{{ $santri->id_santri }}">

            <div class="row">
                <!-- Tagihan Section -->
                <div class="col-md-8">
                    <!-- Tagihan Bulanan -->
                    @if ($tagihan_bulanan->count() > 0)
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-alt"></i> Tagihan Bulanan (Syahriah)
                                    <span class="float-right">
                                        Total: Rp {{ number_format($total_tunggakan_bulanan, 0, ',', '.') }}
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">
                                                    <input type="checkbox" id="selectAllBulanan" class="form-check-input">
                                                </th>
                                                <th>Bulan</th>
                                                <th>Tahun</th>
                                                <th>Nominal</th>
                                                <th>Sudah Dibayar</th>
                                                <th>Sisa</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tagihan_bulanan as $index => $tagihan)
                                                <tr class="tagihan-row" data-type="bulanan"
                                                    data-id="{{ $tagihan->id_tagihan_bulanan }}"
                                                    data-sisa="{{ $tagihan->sisa_tagihan }}">
                                                    <td>
                                                        <input type="checkbox"
                                                            class="form-check-input tagihan-checkbox tagihan-bulanan"
                                                            value="{{ $tagihan->id_tagihan_bulanan }}"
                                                            data-nominal="{{ $tagihan->sisa_tagihan }}"
                                                            {{ $index == 0 ? 'checked' : '' }}>
                                                    </td>
                                                    <td>{{ $tagihan->bulan }}</td>
                                                    <td>{{ $tagihan->tahun }}</td>
                                                    <td>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($tagihan->total_pembayaran, 0, ',', '.') }}
                                                    </td>
                                                    <td class="sisa-tagihan font-weight-bold">
                                                        Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ $tagihan->status_color }}">
                                                            {{ ucfirst(str_replace('_', ' ', $tagihan->status)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tagihan Terjadwal -->
                    @if ($tagihan_terjadwal->count() > 0)
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice-dollar"></i> Tagihan Terjadwal
                                    <span class="float-right">
                                        Total: Rp {{ number_format($total_tunggakan_terjadwal, 0, ',', '.') }}
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">
                                                    <input type="checkbox" id="selectAllTerjadwal" class="form-check-input">
                                                </th>
                                                <th>Nama Tagihan</th>
                                                <th>Tahun</th>
                                                <th>Nominal</th>
                                                <th>Sudah Dibayar</th>
                                                <th>Sisa</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tagihan_terjadwal as $tagihan)
                                                <tr class="tagihan-row" data-type="terjadwal"
                                                    data-id="{{ $tagihan->id_tagihan_terjadwal }}"
                                                    data-sisa="{{ $tagihan->sisa_tagihan }}">
                                                    <td>
                                                        <input type="checkbox"
                                                            class="form-check-input tagihan-checkbox tagihan-terjadwal"
                                                            value="{{ $tagihan->id_tagihan_terjadwal }}"
                                                            data-nominal="{{ $tagihan->sisa_tagihan }}">
                                                    </td>
                                                    <td>{{ $tagihan->daftarBiaya->nama_biaya ?? 'Tagihan Terjadwal' }}</td>
                                                    <td>{{ $tagihan->tahun }}</td>
                                                    <td>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($tagihan->total_pembayaran, 0, ',', '.') }}
                                                    </td>
                                                    <td class="sisa-tagihan font-weight-bold">
                                                        Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $tagihan->status == 'lunas' ? 'success' : ($tagihan->status == 'dibayar_sebagian' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $tagihan->status)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($tagihan_bulanan->count() == 0 && $tagihan_terjadwal->count() == 0)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Tidak ada tagihan yang belum dibayar.
                        </div>
                    @endif
                </div>

                <!-- Payment Input Section -->
                <div class="col-md-4">
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-cash-register"></i> Input Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nominal Pembayaran <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" name="nominal_pembayaran" id="nominalPembayaran"
                                        class="form-control form-control-lg text-right" placeholder="0" required
                                        min="1">
                                </div>
                                <small class="form-text text-muted">
                                    Total tagihan terpilih: <span id="totalTagihanTerpilih" class="font-weight-bold">Rp
                                        0</span>
                                </small>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Pembayaran</label>
                                <input type="date" name="tanggal_pembayaran" class="form-control"
                                    value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                            </div>

                            <div class="form-group">
                                <label>Catatan Pembayaran</label>
                                <textarea name="payment_note" class="form-control" rows="2" placeholder="Opsional (contoh: Cicilan 1 dari 3)"></textarea>
                            </div>

                            <hr>

                            <div id="allocationPreview" style="display: none;">
                                <h6>Preview Alokasi:</h6>
                                <div id="allocationList" class="small"></div>
                                <div id="sisaPembayaran" class="mt-2"></div>
                            </div>

                            <button type="button" id="previewButton" class="btn btn-warning btn-block mb-2" disabled>
                                <i class="fas fa-eye"></i> Preview Pembayaran
                            </button>

                            <button type="submit" id="submitButton" class="btn btn-success btn-block" disabled>
                                <i class="fas fa-check-circle"></i> Proses Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="previewModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmPayment">
                        <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/payment.js') }}"></script>
    <script>
        // Initialize payment form
        $(document).ready(function() {
            const paymentForm = new PaymentForm({
                santriId: {{ $santri->id_santri }},
                csrfToken: '{{ csrf_token() }}',
                previewUrl: '{{ route('pembayaran.preview') }}',
                storeUrl: '{{ route('pembayaran.store') }}'
            });
        });
    </script>
@endsection
