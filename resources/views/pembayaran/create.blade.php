@extends('layouts.home')
@section('title_page', 'Form Pembayaran')

@section('css_inline')
    <style>
        /* Fix z-index conflict between SweetAlert2 and Bootstrap Modal */
        .swal2-container {
            z-index: 10000 !important;
            /* Higher than Bootstrap modal (1050) */
        }

        /* Ensure modal backdrop doesn't interfere */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        /* Fix untuk multiple backdrop */
        .modal-backdrop+.modal-backdrop {
            opacity: 0;
            display: none;
        }

        .modal-backdrop.show {
            opacity: 0 !important;
            /* Transparan sepenuhnya */
            pointer-events: none !important;
            background-color: transparent !important;
        }
    </style>
@endsection
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
                                {{-- PERBAIKAN: Ganti dari kategoriSantri ke sistem kategori yang benar --}}
                                <strong>Kategori:</strong>
                                @if (isset($santri->kategori_biaya_utama_name))
                                    {{ $santri->kategori_biaya_utama_name }}
                                @else
                                    {{ $santri->biayaSantris->first()?->daftarBiaya?->kategoriBiaya?->nama_kategori ?? 'Tanpa Kategori' }}
                                @endif
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
                                                        <span
                                                            class="badge
        @if ($tagihan->status == 'lunas') badge-success @endif
        @if ($tagihan->status == 'dibayar_sebagian') badge-warning @endif
        @if (in_array($tagihan->status, ['belum_lunas', 'belum_bayar'])) badge-danger @endif
    "
                                                            style="
        @if ($tagihan->status == 'lunas') color: white !important; @endif
        @if ($tagihan->status == 'dibayar_sebagian') color: #212529 !important; background-color: #ffc107 !important; @endif
        @if (in_array($tagihan->status, ['belum_lunas', 'belum_bayar'])) color: white !important; @endif
    ">
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
                                                    <td>{{ $tagihan->daftarBiaya->kategoriBiaya->nama_kategori ?? 'Tagihan Terjadwal' }}
                                                    </td>
                                                    <td>{{ $tagihan->tahun }}</td>
                                                    <td>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($tagihan->total_pembayaran, 0, ',', '.') }}
                                                    </td>
                                                    <td class="sisa-tagihan font-weight-bold">
                                                        Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge
        @if ($tagihan->status == 'lunas') badge-success @endif
        @if ($tagihan->status == 'dibayar_sebagian') badge-warning @endif
        @if (in_array($tagihan->status, ['belum_lunas', 'belum_bayar'])) badge-danger @endif
    "
                                                            style="
        @if ($tagihan->status == 'lunas') color: white !important; @endif
        @if ($tagihan->status == 'dibayar_sebagian') color: #212529 !important; background-color: #ffc107 !important; @endif
        @if (in_array($tagihan->status, ['belum_lunas', 'belum_bayar'])) color: white !important; @endif
    ">
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
                                        min="20000">
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Minimal pembayaran: <strong>Rp 20.000</strong><br>
                                    Total tagihan terpilih: <span id="totalTagihanTerpilih" class="font-weight-bold">Rp
                                        0</span>
                                </small>
                                <div id="nominalError" class="invalid-feedback" style="display: none;">
                                    Minimal pembayaran adalah Rp 20.000
                                </div>
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

    <form id="finalPaymentForm" action="{{ route('pembayaran.store') }}" method="POST">
        @csrf

        <input type="hidden" name="santri_id">
        <input type="hidden" name="nominal_pembayaran">
        <input type="hidden" name="tanggal_pembayaran">
        <input type="hidden" name="payment_note">

        <!-- Alokasi pembayaran -->
        <div id="allocationsContainer"></div>

        <input type="hidden" name="sisa_pembayaran">
    </form>

@endsection

@section('script')
    <script src="{{ asset('js/payment.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Fix SweetAlert2 backdrop issue dengan Bootstrap Modal
            // Remove any SweetAlert backdrop when modal opens
            $('#previewModal').on('show.bs.modal', function() {
                $('.swal2-container').removeClass('swal2-backdrop-show');
                $('.swal2-backdrop').remove();
            });

            // Clean up when modal closes
            $('#previewModal').on('hidden.bs.modal', function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            });

            // Initialize payment form
            const paymentForm = new PaymentForm({
                santriId: {{ $santri->id_santri }},
                csrfToken: '{{ csrf_token() }}',
                previewUrl: '{{ route('pembayaran.preview') }}',
                storeUrl: '{{ route('pembayaran.store') }}'
            });

            // Set global variable untuk debugging
            window.paymentFormInstance = paymentForm;
        });
    </script>
@endsection
