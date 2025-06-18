@extends('layouts.home')
@section('title_page', 'Daftar Tagihan Terjadwal')

@section('content')
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Daftar Tagihan Terjadwal</h2>
                    @if (auth()->user()->hasRole('admin'))
                        <div class="btn-group">
                            <a href="{{ route('tagihan_terjadwal.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Tagihan Individual
                            </a>
                            <a href="{{ route('tagihan_terjadwal.createBulkTerjadwal') }}" class="btn btn-info">
                                <i class="fas fa-layer-group"></i> Generate Tagihan Massal
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter"></i> Filter & Export
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('tagihan_terjadwal.index') }}" id="filterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tahun">Tahun</label>
                                        <select name="tahun" id="tahun" class="form-control">
                                            <option value="">-- Semua Tahun --</option>
                                            @foreach ($tahunOptions as $tahun)
                                                <option value="{{ $tahun }}"
                                                    {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                                    {{ $tahun }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">-- Semua Status --</option>
                                            @foreach ($statusOptions as $status)
                                                <option value="{{ $status }}"
                                                    {{ request('status') == $status ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="jenis_biaya">Jenis Biaya</label>
                                        <select name="jenis_biaya" id="jenis_biaya" class="form-control">
                                            <option value="">-- Semua Jenis --</option>
                                            @foreach ($jenisBiayaOptions as $jenisBiaya)
                                                <option value="{{ $jenisBiaya->id_kategori_biaya }}"
                                                    {{ request('jenis_biaya') == $jenisBiaya->id_kategori_biaya ? 'selected' : '' }}>
                                                    {{ $jenisBiaya->nama_kategori }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="search">Cari Santri</label>
                                        <input type="text" name="search" id="search" class="form-control"
                                            placeholder="Nama atau NIS..." value="{{ request('search') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('tagihan_terjadwal.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Reset
                                        </a>
                                        <button type="button" class="btn btn-success" onclick="exportData()">
                                            <i class="fas fa-file-excel"></i> Export Excel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Tagihan Terjadwal</h5>
                        <small class="text-muted">
                            Total: {{ $tagihanTerjadwals->total() }} tagihan
                        </small>
                    </div>
                    <div class="card-body">
                        @if ($tagihanTerjadwals->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">Nama Santri</th>
                                            <th width="10%">NIS</th>
                                            <th width="15%">Jenis Biaya</th>
                                            <th width="12%">Nominal Tagihan</th>
                                            <th width="8%">Tahun</th>
                                            <th width="10%">Tahun Ajar</th>
                                            <th width="10%">Status</th>
                                            @if (auth()->user()->hasRole('admin'))
                                                <th width="15%">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tagihanTerjadwals as $index => $tagihan)
                                            <tr>
                                                <td>{{ $tagihanTerjadwals->firstItem() + $index }}</td>
                                                <td>
                                                    <strong>{{ $tagihan->santri->nama_santri ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ $tagihan->santri->nis ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $tagihan->daftarBiaya->kategoriBiaya->nama_kategori ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    <strong class="text-primary">
                                                        Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                                    </strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary">{{ $tagihan->tahun }}</span>
                                                </td>
                                                <td>{{ $tagihan->tahunAjar->tahun_ajar ?? '-' }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = '';
                                                        $statusText = '';
                                                        switch ($tagihan->status) {
                                                            case 'belum_lunas':
                                                                $statusClass = 'badge-danger';
                                                                $statusText = 'Belum Lunas';
                                                                break;
                                                            case 'dibayar_sebagian':
                                                                $statusClass = 'badge-warning';
                                                                $statusText = 'Dibayar Sebagian';
                                                                break;
                                                            case 'lunas':
                                                                $statusClass = 'badge-success';
                                                                $statusText = 'Lunas';
                                                                break;
                                                            default:
                                                                $statusClass = 'badge-secondary';
                                                                $statusText = ucfirst($tagihan->status);
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                @if (auth()->user()->hasRole('admin'))
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="{{ route('tagihan_terjadwal.edit', $tagihan->id_tagihan_terjadwal) }}"
                                                                class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="confirmDelete({{ $tagihan->id_tagihan_terjadwal }})"
                                                                title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>

                                                        <!-- Hidden delete form -->
                                                        <form id="delete-form-{{ $tagihan->id_tagihan_terjadwal }}"
                                                            action="{{ route('tagihan_terjadwal.destroy', $tagihan->id_tagihan_terjadwal) }}"
                                                            method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $tagihanTerjadwals->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada data tagihan terjadwal</h5>
                                <p class="text-muted">Gunakan filter di atas atau buat tagihan baru.</p>
                                @if (auth()->user()->hasRole('admin'))
                                    <a href="{{ route('tagihan_terjadwal.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Buat Tagihan Baru
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal for Export -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 mb-0">Sedang memproses export data...</p>
                    <small class="text-muted">Mohon tunggu beberapa saat</small>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Auto submit form when filter changes
            $('#tahun, #status, #jenis_biaya').on('change', function() {
                $('#filterForm').submit();
            });

            // Search with delay
            let searchTimeout;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    $('#filterForm').submit();
                }, 500);
            });
        });

        function confirmDelete(id) {
            if (confirm(
                    'Apakah Anda yakin ingin menghapus tagihan ini?\n\nPerhatian: Data yang sudah dihapus dapat di-restore kembali.'
                )) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        function exportData() {
            // Show loading modal
            $('#exportModal').modal('show');

            // Get current filter parameters
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            // Create export URL with current filters
            const exportUrl = '{{ route('tagihan_terjadwal.export') }}?' + params.toString();

            // Create temporary link and click it
            const link = document.createElement('a');
            link.href = exportUrl;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Hide loading modal after a short delay
            setTimeout(function() {
                $('#exportModal').modal('hide');
            }, 2000);
        }

        // Hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
@endsection

@section('css_inline')
    <style>
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-top: none;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.875rem;
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.775rem;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
@endsection
