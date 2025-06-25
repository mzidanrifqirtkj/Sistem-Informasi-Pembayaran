@extends('layouts.home')
@section('title_page', 'Pembayaran Massal')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-8">
                <h1>Pembayaran Massal</h1>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('pembayaran.bulk.import') }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Import Excel
                </a>
                <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        @if (session('bulk_results'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Hasil Pembayaran Massal</h5>
                        </div>
                        <div class="card-body">
                            @php $results = session('bulk_results'); @endphp

                            @if (count($results['success']) > 0)
                                <h6>Berhasil ({{ count($results['success']) }})</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-success">
                                        <thead>
                                            <tr>
                                                <th>NIS</th>
                                                <th>Nama</th>
                                                <th>Nominal</th>
                                                <th>No. Kwitansi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($results['success'] as $success)
                                                <tr>
                                                    <td>{{ $success['nis'] }}</td>
                                                    <td>{{ $success['santri'] }}</td>
                                                    <td>Rp {{ number_format($success['nominal'], 0, ',', '.') }}</td>
                                                    <td>{{ $success['receipt'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if (count($results['failed']) > 0)
                                <h6>Gagal ({{ count($results['failed']) }})</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-danger">
                                        <thead>
                                            <tr>
                                                <th>NIS</th>
                                                <th>Nama</th>
                                                <th>Error</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($results['failed'] as $failed)
                                                <tr>
                                                    <td>{{ $failed['nis'] }}</td>
                                                    <td>{{ $failed['santri'] }}</td>
                                                    <td>{{ $failed['error'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form id="bulkPaymentForm" action="{{ route('pembayaran.bulk.process') }}" method="POST">
            @csrf

            <!-- Filter Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Filter Santri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kategori Santri</label>
                                <select name="filter_kategori" class="form-control select2" onchange="filterSantri()">
                                    <option value="">-- Semua Kategori --</option>
                                    @foreach (\App\Models\KategoriSantri::all() as $kategori)
                                        <option value="{{ $kategori->id_kategori_santri }}">
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kelas</label>
                                <select name="filter_kelas" class="form-control select2" onchange="filterSantri()">
                                    <option value="">-- Semua Kelas --</option>
                                    @foreach (['1', '2', '3', '4', '5', '6'] as $kelas)
                                        <option value="{{ $kelas }}">Kelas {{ $kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-primary" onclick="selectAll()">
                                        <i class="fas fa-check-square"></i> Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="deselectAll()">
                                        <i class="fas fa-square"></i> Batal Pilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Options -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Opsi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipe Pembayaran <span class="text-danger">*</span></label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="payment_type" id="same_amount"
                                            value="same_amount" checked onchange="togglePaymentType()">
                                        <label class="form-check-label" for="same_amount">
                                            Nominal Sama untuk Semua
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="payment_type" id="individual"
                                            value="individual" onchange="togglePaymentType()">
                                        <label class="form-check-label" for="individual">
                                            Nominal Per Santri
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" id="sameAmountDiv">
                                <label>Nominal Pembayaran <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" name="nominal_pembayaran" class="form-control" placeholder="0"
                                        min="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pembayaran untuk Bulan Tertentu?</label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="for_month"
                                            onchange="toggleMonthSelection()">
                                        <label class="form-check-label" for="for_month">
                                            Ya, untuk bulan tertentu
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="monthSelectionDiv" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bulan</label>
                                            <select name="bulan" class="form-control">
                                                @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                                                    <option value="{{ $month }}">{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tahun</label>
                                            <select name="tahun" class="form-control">
                                                @for ($year = date('Y'); $year >= 2020; $year--)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan Pembayaran</label>
                        <textarea name="payment_note" class="form-control" rows="2"
                            placeholder="Opsional (contoh: Pembayaran SPP Januari 2025)"></textarea>
                    </div>
                </div>
            </div>

            <!-- Santri List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        Pilih Santri
                        <span class="badge badge-primary" id="selectedCount">0</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="santriTable">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                    </th>
                                    <th width="10%">NIS</th>
                                    <th>Nama</th>
                                    <th width="15%">Kategori</th>
                                    <th width="10%">Kelas</th>
                                    <th width="15%">Total Tunggakan</th>
                                    <th width="20%" id="nominalHeader" style="display: none;">Nominal Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($santris as $santri)
                                    @php
                                        $tunggakanBulanan = $santri
                                            ->tagihanBulanan()
                                            ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
                                            ->sum(
                                                \DB::raw('nominal - COALESCE((
                                        SELECT SUM(nominal_pembayaran)
                                        FROM pembayarans
                                        WHERE tagihan_bulanan_id = tagihan_bulanans.id_tagihan_bulanan
                                        AND is_void = false
                                    ), 0)'),
                                            );

                                        $tunggakanTerjadwal = $santri
                                            ->tagihanTerjadwal()
                                            ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
                                            ->sum(
                                                \DB::raw('nominal - COALESCE((
                                        SELECT SUM(nominal_pembayaran)
                                        FROM pembayarans
                                        WHERE tagihan_terjadwal_id = tagihan_terjadwals.id_tagihan_terjadwal
                                        AND is_void = false
                                    ), 0)'),
                                            );

                                        $totalTunggakan = $tunggakanBulanan + $tunggakanTerjadwal;
                                    @endphp
                                    <tr class="santri-row" data-kategori="{{ $santri->kategori_santri_id }}"
                                        data-kelas="{{ $santri->kelas ?? '' }}">
                                        <td class="text-center">
                                            <input type="checkbox" name="santri_ids[]" value="{{ $santri->id_santri }}"
                                                class="santri-checkbox" onchange="updateSelectedCount()">
                                        </td>
                                        <td>{{ $santri->nis }}</td>
                                        <td>{{ $santri->nama_santri }}</td>
                                        <td>{{ $santri->kategoriSantri->nama_kategori }}</td>
                                        <td class="text-center">{{ $santri->kelas ?? '-' }}</td>
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
                                        <td class="individual-amount" style="display: none;">
                                            <input type="number" name="individual_amounts[{{ $santri->id_santri }}]"
                                                class="form-control form-control-sm text-right" placeholder="0"
                                                min="1" value="{{ $totalTunggakan > 0 ? $totalTunggakan : '' }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-lg" onclick="return confirmBulkPayment()">
                        <i class="fas fa-check-circle"></i> Proses Pembayaran Massal
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });

        function togglePaymentType() {
            const paymentType = $('input[name="payment_type"]:checked').val();

            if (paymentType === 'same_amount') {
                $('#sameAmountDiv').show();
                $('#nominalHeader').hide();
                $('.individual-amount').hide();
            } else {
                $('#sameAmountDiv').hide();
                $('#nominalHeader').show();
                $('.individual-amount').show();
            }
        }

        function toggleMonthSelection() {
            if ($('#for_month').is(':checked')) {
                $('#monthSelectionDiv').show();
            } else {
                $('#monthSelectionDiv').hide();
            }
        }

        function filterSantri() {
            const kategori = $('select[name="filter_kategori"]').val();
            const kelas = $('select[name="filter_kelas"]').val();

            $('.santri-row').each(function() {
                const row = $(this);
                let show = true;

                if (kategori && row.data('kategori') != kategori) {
                    show = false;
                }

                if (kelas && row.data('kelas') != kelas) {
                    show = false;
                }

                if (show) {
                    row.show();
                } else {
                    row.hide();
                    row.find('.santri-checkbox').prop('checked', false);
                }
            });

            updateSelectedCount();
        }

        function selectAll() {
            $('.santri-row:visible .santri-checkbox').prop('checked', true);
            updateSelectedCount();
        }

        function deselectAll() {
            $('.santri-checkbox').prop('checked', false);
            updateSelectedCount();
        }

        function toggleSelectAll() {
            const isChecked = $('#selectAllCheckbox').is(':checked');
            $('.santri-row:visible .santri-checkbox').prop('checked', isChecked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const count = $('.santri-checkbox:checked').length;
            $('#selectedCount').text(count);

            // Update select all checkbox
            const visibleCount = $('.santri-row:visible .santri-checkbox').length;
            const checkedCount = $('.santri-row:visible .santri-checkbox:checked').length;

            if (visibleCount > 0 && visibleCount === checkedCount) {
                $('#selectAllCheckbox').prop('checked', true);
            } else {
                $('#selectAllCheckbox').prop('checked', false);
            }
        }

        function confirmBulkPayment() {
            const selectedCount = $('.santri-checkbox:checked').length;

            if (selectedCount === 0) {
                Swal.fire('Peringatan', 'Pilih minimal 1 santri', 'warning');
                return false;
            }

            const paymentType = $('input[name="payment_type"]:checked').val();

            if (paymentType === 'same_amount') {
                const nominal = $('input[name="nominal_pembayaran"]').val();
                if (!nominal || nominal <= 0) {
                    Swal.fire('Peringatan', 'Masukkan nominal pembayaran', 'warning');
                    return false;
                }
            }

            return confirm(`Proses pembayaran untuk ${selectedCount} santri?`);
        }
    </script>
@endsection
