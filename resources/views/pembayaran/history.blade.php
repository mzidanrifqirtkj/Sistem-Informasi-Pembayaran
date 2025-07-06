@extends('layouts.home')
@section('title_page', 'Riwayat Pembayaran')

@section('css_inline')
    <style>
        .swal2-container {
            z-index: 10000 !important;
        }

        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        .modal-backdrop+.modal-backdrop {
            opacity: 0;
            display: none;
        }

        .modal-backdrop.show {
            opacity: 0 !important;
            pointer-events: none !important;
            background-color: transparent !important;
        }
    </style>
@endsection

@section('content')
    @php
        $user = auth()->user();
    @endphp
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-8">
                <h1>Riwayat Pembayaran</h1>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('pembayaran.history') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Aktif</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Void</option>
                        </select>
                    </div>
                    @unless (auth()->user()->hasRole('santri'))
                        <div class="col-md-2">
                            <label>Cari</label>
                            <input type="text" name="search" class="form-control" placeholder="No/Nama/NIS"
                                value="{{ request('search') }}">
                        </div>
                    @endunless
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('pembayaran.history') }}" class="btn btn-secondary">
                                <i class="fas fa-sync"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Hari Ini</h6>
                        <h4>Rp
                            @php
                                try {
                                    echo number_format(
                                        $user->hasRole('santri') && $user->santri
                                            ? \App\Models\Pembayaran::getTodayTotalForSantri($user->santri->id_santri)
                                            : \App\Models\Pembayaran::getTodayTotal(),
                                        0,
                                        ',',
                                        '.',
                                    );
                                } catch (\Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Total Bulan Ini</h6>
                        <h4>Rp
                            @php
                                try {
                                    echo number_format(
                                        $user->hasRole('santri') && $user->santri
                                            ? \App\Models\Pembayaran::getMonthTotalForSantri($user->santri->id_santri)
                                            : \App\Models\Pembayaran::getMonthTotal(),
                                        0,
                                        ',',
                                        '.',
                                    );
                                } catch (\Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Total Tahun Ini</h6>
                        <h4>Rp
                            @php
                                try {
                                    echo number_format(
                                        $user->hasRole('santri') && $user->santri
                                            ? \App\Models\Pembayaran::getYearTotalForSantri($user->santri->id_santri)
                                            : \App\Models\Pembayaran::getYearTotal(),
                                        0,
                                        ',',
                                        '.',
                                    );
                                } catch (\Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Transaksi Hari Ini</h6>
                        <h4>
                            @php
                                try {
                                    echo \App\Models\Pembayaran::whereDate('tanggal_pembayaran', today())
                                        ->where('is_void', false)
                                        ->count();
                                } catch (\Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">No. Kwitansi</th>
                                <th width="10%">Tanggal</th>
                                <th width="10%">NIS</th>
                                <th>Nama Santri</th>
                                <th>Deskripsi</th>
                                <th width="15%">Nominal</th>
                                <th width="8%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembayarans as $index => $pembayaran)
                                <tr class="{{ $pembayaran->is_void ? 'table-danger' : '' }}">
                                    <td>{{ $pembayarans->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $pembayaran->receipt_number ?? '-' }}</strong>
                                    </td>
                                    <td>{{ $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('d/m/Y') : '-' }}
                                    </td>
                                    <td>
                                        @php
                                            try {
                                                echo $pembayaran->santri_nis;
                                            } catch (\Exception $e) {
                                                echo '-';
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                            try {
                                                echo $pembayaran->santri_name;
                                            } catch (\Exception $e) {
                                                echo 'Tidak Diketahui';
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                            try {
                                                echo $pembayaran->payment_description;
                                                if ($pembayaran->payment_note) {
                                                    echo '<br><small class="text-muted">' .
                                                        e($pembayaran->payment_note) .
                                                        '</small>';
                                                }
                                            } catch (\Exception $e) {
                                                echo 'Pembayaran';
                                            }
                                        @endphp
                                    </td>
                                    <td class="text-right">
                                        <strong>
                                            @php
                                                try {
                                                    echo $pembayaran->formatted_nominal;
                                                } catch (\Exception $e) {
                                                    echo 'Rp ' .
                                                        number_format(
                                                            $pembayaran->nominal_pembayaran ?? 0,
                                                            0,
                                                            ',',
                                                            '.',
                                                        );
                                                }
                                            @endphp
                                        </strong>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            try {
                                                echo $pembayaran->status_badge;
                                            } catch (\Exception $e) {
                                                if ($pembayaran->is_void) {
                                                    echo '<span class="badge badge-danger">Void</span>';
                                                } else {
                                                    echo '<span class="badge badge-success">Success</span>';
                                                }
                                            }
                                        @endphp
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('pembayaran.receipt', $pembayaran->id_pembayaran) }}"
                                            class="btn btn-sm btn-info" target="_blank" title="Lihat Kwitansi">
                                            <i class="fas fa-print"></i>
                                        </a>

                                        {{-- FIXED: Use correct permission check --}}
                                        @can('pembayaran.void')
                                            @php
                                                $canVoid = false;
                                                try {
                                                    $canVoid = $pembayaran->can_void;
                                                } catch (\Exception $e) {
                                                    $canVoid = false;
                                                }
                                            @endphp
                                            @if ($canVoid)
                                                <button onclick="showVoidModal({{ $pembayaran->id_pembayaran }})"
                                                    class="btn btn-sm btn-danger" title="Void Pembayaran">
                                                    <i class="fas fa-ban"></i> Void
                                                </button>
                                            @endif
                                        @endcan

                                        @if ($pembayaran->is_void)
                                            <button class="btn btn-sm btn-secondary"
                                                onclick="showVoidInfo({{ $pembayaran->id_pembayaran }})" title="Info Void">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data pembayaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan {{ $pembayarans->firstItem() ?? 0 }} - {{ $pembayarans->lastItem() ?? 0 }}
                        dari {{ $pembayarans->total() }} pembayaran
                    </div>
                    <div>
                        {{ $pembayarans->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Void Modal -->
    <div class="modal fade" id="voidModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="voidForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Void Pembayaran</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin void pembayaran ini?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Pembayaran yang di-void tidak dapat dikembalikan.
                        </div>
                        <div class="form-group">
                            <label>Alasan Void <span class="text-danger">*</span></label>
                            <textarea name="void_reason" class="form-control" rows="3" placeholder="Masukkan alasan void..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban"></i> Void Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Void Info Modal -->
    <div class="modal fade" id="voidInfoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informasi Void</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="voidInfoContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function showVoidModal(id) {
            const form = document.getElementById('voidForm');
            // FIXED: Use correct route format
            form.action = `{{ route('pembayaran.void.process', '') }}/${id}`;
            form.querySelector('textarea[name="void_reason"]').value = '';
            $('#voidModal').modal('show');
        }

        function showVoidInfo(id) {
            // FIXED: Use correct route for void info
            $.get(`{{ route('pembayaran.void.info', '') }}/${id}`, function(data) {
                let content = `
            <table class="table table-sm">
                <tr>
                    <td width="40%">Di-void oleh</td>
                    <td>: ${data.voided_by_name}</td>
                </tr>
                <tr>
                    <td>Tanggal Void</td>
                    <td>: ${data.voided_at}</td>
                </tr>
                <tr>
                    <td>Alasan</td>
                    <td>: ${data.void_reason || 'Tidak ada alasan'}</td>
                </tr>
            </table>
        `;
                $('#voidInfoContent').html(content);
                $('#voidInfoModal').modal('show');
            }).fail(function(xhr) {
                console.error('Error loading void info:', xhr);
                alert('Gagal memuat informasi void');
            });
        }

        // Auto submit filter on date change
        $('input[type="date"]').on('change', function() {
            if ($('input[name="start_date"]').val() && $('input[name="end_date"]').val()) {
                $(this).closest('form').submit();
            }
        });

        // Handle void form submission with AJAX
        $('#voidForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Disable button dan show loading
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#voidModal').modal('hide');

                    if (response.success) {
                        // Show success message and reload
                        alert(response.message || 'Pembayaran berhasil di-void');
                        location.reload();
                    } else {
                        alert(response.message || 'Terjadi kesalahan');
                    }
                },
                error: function(xhr) {
                    let message = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    alert(message);
                },
                complete: function() {
                    // Re-enable button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>
@endsection
