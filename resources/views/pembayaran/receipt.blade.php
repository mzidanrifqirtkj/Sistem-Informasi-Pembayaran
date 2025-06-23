@extends('layouts.home')
@section('title_page', 'Kwitansi Pembayaran')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>Kwitansi Pembayaran</h1>
                    <div>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                        <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card" id="printArea">
                    <div class="card-body">
                        @if ($isReprint)
                            <div class="watermark">COPY</div>
                        @endif

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h3>KWITANSI PEMBAYARAN</h3>
                            <p class="mb-0">No: <strong>{{ $pembayaran->receipt_number }}</strong></p>
                        </div>

                        @php
                            // SAFE: Gunakan accessor yang sudah dibuat di model
                            $santri = $pembayaran->santri; // This uses the getSantriAttribute() accessor
                            $santriNis = $pembayaran->santri_nis; // This uses getSantriNisAttribute() accessor
                            $santriName = $pembayaran->santri_name; // This uses getSantriNameAttribute() accessor

                            // Safe category access
                            $kategoriName = 'Tidak Ada Data';
                            if ($santri && $santri->kategoriSantri) {
                                $kategoriName = $santri->kategoriSantri->nama_kategori;
                            }
                        @endphp

                        <!-- Info Pembayaran -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">Telah Terima Dari</td>
                                        <td>: {{ $santriName ?: 'Data Santri Tidak Ditemukan' }}</td>
                                    </tr>
                                    <tr>
                                        <td>NIS</td>
                                        <td>: {{ $santriNis ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kategori</td>
                                        <td>: {{ $kategoriName }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">Tanggal Bayar</td>
                                        <td>: {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jam</td>
                                        <td>: {{ $pembayaran->created_at->format('H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Petugas</td>
                                        <td>: {{ $pembayaran->createdBy->name ?? 'System' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Nominal -->
                        <div class="text-center mb-4 p-3 bg-light">
                            <h4 class="mb-1">Jumlah Pembayaran:</h4>
                            <h2 class="text-primary mb-0">Rp
                                {{ number_format($pembayaran->nominal_pembayaran, 0, ',', '.') }}</h2>
                            <p class="mb-0"><em>{{ ucwords(terbilang($pembayaran->nominal_pembayaran)) }} Rupiah</em></p>
                        </div>

                        <!-- Detail Pembayaran -->
                        <h5>Untuk Pembayaran:</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Keterangan</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($pembayaran->payment_type == 'allocated' && $pembayaran->paymentAllocations->count() > 0)
                                        {{-- MODERN: Allocated payment system --}}
                                        @foreach ($pembayaran->paymentAllocations as $index => $allocation)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @if ($allocation->tagihan_bulanan_id && $allocation->tagihanBulanan)
                                                        Syahriah {{ $allocation->tagihanBulanan->bulan }}
                                                        {{ $allocation->tagihanBulanan->tahun }}
                                                    @elseif ($allocation->tagihan_terjadwal_id && $allocation->tagihanTerjadwal)
                                                        {{ $allocation->tagihanTerjadwal->daftarBiaya->kategoriBiaya->nama_kategori ?? 'Tagihan Terjadwal' }}
                                                    @else
                                                        Pembayaran
                                                    @endif
                                                </td>
                                                <td class="text-right">Rp
                                                    {{ number_format($allocation->allocated_amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach

                                        {{-- Show overpayment if any --}}
                                        @if ($pembayaran->sisa_pembayaran > 0)
                                            <tr class="table-warning">
                                                <td>{{ $pembayaran->paymentAllocations->count() + 1 }}</td>
                                                <td><em>Kelebihan Pembayaran (Dikembalikan)</em></td>
                                                <td class="text-right"><em>Rp
                                                        {{ number_format($pembayaran->sisa_pembayaran, 0, ',', '.') }}</em>
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        {{-- LEGACY: Direct payment system --}}
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                @if ($pembayaran->tagihan_bulanan_id && $pembayaran->tagihanBulanan)
                                                    Syahriah {{ $pembayaran->tagihanBulanan->bulan }}
                                                    {{ $pembayaran->tagihanBulanan->tahun }}
                                                @elseif ($pembayaran->tagihan_terjadwal_id && $pembayaran->tagihanTerjadwal)
                                                    {{ $pembayaran->tagihanTerjadwal->daftarBiaya->kategoriBiaya->nama_kategori ?? 'Tagihan Terjadwal' }}
                                                @else
                                                    Pembayaran
                                                @endif
                                            </td>
                                            <td class="text-right">Rp
                                                {{ number_format($pembayaran->nominal_pembayaran, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="table-active">
                                        <th colspan="2" class="text-right">Total Diterima:</th>
                                        <th class="text-right">Rp
                                            {{ number_format($pembayaran->nominal_pembayaran, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if ($pembayaran->payment_note)
                            <p><strong>Catatan:</strong> {{ $pembayaran->payment_note }}</p>
                        @endif

                        {{-- Payment Summary untuk allocated payments --}}
                        @if ($pembayaran->payment_type == 'allocated')
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <strong>Ringkasan Pembayaran:</strong><br>
                                        Total Diterima: Rp
                                        {{ number_format($pembayaran->nominal_pembayaran, 0, ',', '.') }}<br>
                                        Total Dialokasikan: Rp
                                        {{ number_format($pembayaran->paymentAllocations->sum('allocated_amount'), 0, ',', '.') }}<br>
                                        @if ($pembayaran->sisa_pembayaran > 0)
                                            Sisa/Kelebihan: Rp
                                            {{ number_format($pembayaran->sisa_pembayaran, 0, ',', '.') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Footer -->
                        <div class="row mt-5">
                            <div class="col-md-6 text-center">
                                <p>Penyetor,</p>
                                <br><br><br>
                                <p>(.............................)</p>
                            </div>
                            <div class="col-md-6 text-center">
                                <p>Petugas,</p>
                                <br><br><br>
                                <p><u>{{ $pembayaran->createdBy->name ?? 'System' }}</u></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css_inline')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #printArea,
            #printArea * {
                visibility: visible;
            }

            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .btn,
            .navbar,
            .sidebar {
                display: none !important;
            }
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(0, 0, 0, 0.1);
            font-weight: bold;
            z-index: 1;
        }

        #printArea {
            position: relative;
        }
    </style>
@endsection

{{-- Fungsi terbilang() sudah tersedia di helpers.php --}}
