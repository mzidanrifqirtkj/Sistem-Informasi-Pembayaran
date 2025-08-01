@extends('layouts.home')
@section('title_page', 'Pembayaran Santri')

@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-8">
                <h1>Pilih Santri untuk Membayar Tagihan</h1>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2 justify-content-end">
                    @can('pembayaran-bulk')
                        <a href="{{ route('pembayaran.bulk.index') }}" class="btn btn-success">
                            <i class="fas fa-users"></i> Pembayaran Massal
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('pembayaran.index') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Cari NIS atau Nama Santri"
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="kategori" class="form-control select2">
                            <option value="">-- Semua Kategori --</option>
                            @foreach (\App\Models\KategoriBiaya::where('status', 'jalur')->get() as $kategori)
                                <option value="{{ $kategori->id_kategori_biaya }}"
                                    {{ request('kategori') == $kategori->id_kategori_biaya ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="santriTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIS</th>
                                <th>Nama</th>
                                <th width="20%">Kategori</th>
                                <th width="15%">Total Tunggakan</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($santris as $index => $santri)
                                @php
                                    // FIXED: Gunakan accessor baru yang sudah menghitung dengan benar
                                    $totalTunggakan = $santri->total_tunggakan;
                                @endphp
                                <tr>
                                    <td>{{ $santris->firstItem() + $index }}</td>
                                    <td>{{ $santri->nis }}</td>
                                    <td>
                                        <a href="{{ route('santri.show', $santri->id_santri) }}" target="_blank">
                                            {{ $santri->nama_santri }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $santri->kategori_biaya_utama_name }}
                                        </span>
                                        @if ($santri->all_kategori_biaya->count() > 1)
                                            <br><small class="text-muted">
                                                + {{ $santri->all_kategori_biaya->count() - 1 }} kategori lain
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($totalTunggakan > 0)
                                            <span class="text-danger font-weight-bold">
                                                Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-success">
                                                <i class="fas fa-check-circle"></i> Lunas
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('pembayaran.show', $santri->id_santri) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-money-bill-wave"></i> Bayar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data santri aktif</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan {{ $santris->firstItem() ?? 0 }} - {{ $santris->lastItem() ?? 0 }}
                        dari {{ $santris->total() }} santri
                    </div>
                    <div>
                        {{ $santris->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>
@endsection
