@extends('layouts.home')

@section('title_page', 'Buat Tagihan Bulanan Individual')

@section('content')
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Enhanced form styling */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-label.required::after {
            content: ' *';
            color: #dc3545;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        /* Enhanced Select2 styling */
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 10px !important;
            border: 1px solid #dee2e6 !important;
            padding: 0.375rem 0.75rem !important;
            min-height: 48px !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding: 0.375rem 0 !important;
            line-height: 1.5 !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15) !important;
        }

        .select2-dropdown {
            border-radius: 10px !important;
            border: 1px solid #dee2e6 !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* Button styling */
        .btn {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
        }

        /* Info section styling */
        .info-section {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .info-section h5 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .rincian-table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .rincian-table table {
            margin-bottom: 0;
        }

        .rincian-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            padding: 0.75rem;
            border-bottom: 2px solid #dee2e6;
        }

        .rincian-table td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .total-section {
            background-color: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .total-section strong {
            color: #004085;
            font-size: 1.25rem;
        }

        /* Yearly Data Table Styling */
        .yearly-data-section {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
            border: 1px solid #e9ecef;
        }

        .yearly-table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .yearly-table table {
            margin-bottom: 0;
        }

        .yearly-table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
            white-space: nowrap;
        }

        .yearly-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .yearly-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .yearly-table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Status styling */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-lunas {
            background-color: #d1edff;
            color: #198754;
        }

        .status-dibayar-sebagian {
            background-color: #fff3cd;
            color: #fd7e14;
        }

        .status-belum-lunas {
            background-color: #f8d7da;
            color: #dc3545;
        }

        .status-belum-ada {
            background-color: #e2e3e5;
            color: #6c757d;
        }

        /* Alert styling */
        .alert {
            border-radius: 10px;
            border: none;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        /* Loading animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Summary cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
        }

        .summary-card h6 {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .summary-card .value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .summary-card .subtitle {
            font-size: 0.75rem;
            color: #6c757d;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }

            .yearly-table {
                font-size: 0.875rem;
            }

            .summary-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-invoice fa-2x text-primary me-3"></i>
                            <div>
                                <h4 class="mb-0">Buat Tagihan Bulanan Individual</h4>
                                <small class="text-muted">Isi form untuk membuat tagihan bulanan santri</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-circle me-2"></i>Terdapat kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('tagihan_bulanan.store') }}" id="createForm">
                            @csrf

                            <!-- Santri Selection -->
                            <div class="mb-4">
                                <label for="santri_id" class="form-label required">Pilih Santri</label>
                                <select name="santri_id" id="santri_id" class="form-select select2" required>
                                    <option value="">-- Pilih Santri --</option>
                                    @foreach ($santris as $santri)
                                        @if (
                                            $santri->biayaSantris()->whereHas('daftarBiaya.kategoriBiaya', function ($q) {
                                                    $q->whereIn('status', ['tambahan', 'jalur']);
                                                })->exists())
                                            <option value="{{ $santri->id_santri }}"
                                                {{ old('santri_id') == $santri->id_santri ? 'selected' : '' }}
                                                data-kelas="{{ $santri->kelasAktif->nama_kelas ?? 'Tanpa Kelas' }}">
                                                {{ $santri->nama_santri }} - {{ $santri->nis }}
                                                ({{ $santri->kelasAktif->nama_kelas ?? 'Tanpa Kelas' }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Hanya menampilkan santri yang memiliki biaya
                                    tambahan/jalur
                                </small>
                            </div>

                            <!-- Month and Year Selection -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="bulan" class="form-label required">Bulan</label>
                                    <select name="bulan" id="bulan" class="form-select" required>
                                        <option value="">-- Pilih Bulan --</option>
                                        @php
                                            $monthNames = [
                                                'Jan' => 'Januari',
                                                'Feb' => 'Februari',
                                                'Mar' => 'Maret',
                                                'Apr' => 'April',
                                                'May' => 'Mei',
                                                'Jun' => 'Juni',
                                                'Jul' => 'Juli',
                                                'Aug' => 'Agustus',
                                                'Sep' => 'September',
                                                'Oct' => 'Oktober',
                                                'Nov' => 'November',
                                                'Dec' => 'Desember',
                                            ];
                                        @endphp
                                        @foreach ($months as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ old('bulan') == $key ? 'selected' : '' }}>
                                                {{ $monthNames[$key] ?? $key }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="tahun" class="form-label required">Tahun</label>
                                    <select name="tahun" id="tahun" class="form-select" required>
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}"
                                                {{ old('tahun', date('Y')) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Biaya Info Section -->
                            <div id="biayaInfo" class="info-section" style="display: none;">
                                <h5>
                                    <i class="fas fa-list-alt me-2"></i>Rincian Biaya
                                </h5>
                                <div id="rincianList" class="rincian-table">
                                    <div class="text-center p-3">
                                        <div class="loading-spinner"></div>
                                        <p class="mb-0 mt-2 text-muted">Memuat data biaya...</p>
                                    </div>
                                </div>
                                <div class="total-section">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark">Total Tagihan:</span>
                                        <strong id="totalNominal">Rp 0</strong>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tagihan_bulanan.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                    <i class="fas fa-save me-2"></i>Simpan Tagihan
                                </button>
                            </div>
                        </form>

                        <!-- Yearly Data Section -->
                        <div id="yearlyDataSection" class="yearly-data-section" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-alt me-2"></i>Data Tagihan Santri Tahun <span
                                        id="selectedYear"></span>
                                </h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshYearlyData()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            </div>

                            <!-- Summary Cards -->
                            <div class="summary-cards" id="summaryCards">
                                <!-- Summary cards will be populated by JavaScript -->
                            </div>

                            <!-- Data Table -->
                            <div class="yearly-table" id="yearlyDataTable">
                                <div class="text-center p-4">
                                    <div class="loading-spinner"></div>
                                    <p class="mb-0 mt-2 text-muted">Memuat data tagihan tahunan...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Include Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Santri --',
                allowClear: true,
                width: '100%'
            });

            // Function to check if all required fields are filled
            function checkFormValidity() {
                const santriId = $('#santri_id').val();
                const bulan = $('#bulan').val();
                const tahun = $('#tahun').val();

                if (santriId && bulan && tahun) {
                    $('#submitBtn').prop('disabled', false);
                    loadSantriBiaya(santriId);
                } else {
                    $('#submitBtn').prop('disabled', true);
                    if (!santriId) {
                        $('#biayaInfo').hide();
                    }
                }

                // Load yearly data if santri and year are selected
                if (santriId && tahun) {
                    loadYearlyData(santriId, tahun);
                } else {
                    $('#yearlyDataSection').hide();
                }
            }

            // Event listeners
            $('#santri_id, #bulan, #tahun').on('change', function() {
                checkFormValidity();
            });

            // Load initial data if santri selected
            if ($('#santri_id').val()) {
                checkFormValidity();
            }
        });

        function loadSantriBiaya(santriId) {
            if (!santriId) {
                $('#biayaInfo').hide();
                return;
            }

            $('#biayaInfo').show();
            $('#rincianList').html(`
                <div class="text-center p-3">
                    <div class="loading-spinner"></div>
                    <p class="mb-0 mt-2 text-muted">Memuat data biaya...</p>
                </div>
            `);

            $.ajax({
                url: "{{ route('tagihan_bulanan.getSantriBiayaInfo') }}",
                method: 'GET',
                data: {
                    santri_id: santriId
                },
                success: function(response) {
                    console.log('AJAX response:', response);
                    if (response.success && response.rincian && Object.keys(response.rincian).length > 0) {
                        let html = `
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th width="70%">Jenis Biaya</th>
                                        <th width="30%" class="text-end">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        Object.values(response.rincian).forEach(function(item) {
                            html += `
                                <tr>
                                    <td>
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        ${item.nama ? item.nama : '<span class="text-danger">[Nama kategori tidak ditemukan]</span>'}
                                    </td>
                                    <td class="text-end fw-semibold">
                                        Rp ${number_format(item.nominal)}
                                    </td>
                                </tr>
                            `;
                        });
                        html += '</tbody></table>';
                        $('#rincianList').html(html);
                        $('#totalNominal').text(response.formatted_total);
                        $('#biayaInfo').css('background-color', '#f0f8ff');
                        setTimeout(() => {
                            $('#biayaInfo').css('background-color', '#f8f9fa');
                        }, 500);
                    } else {
                        $('#rincianList').html(`
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Santri tidak memiliki alokasi biaya kategori tambahan/jalur
                            </div>
                        `);
                        $('#totalNominal').text('Rp 0');
                        $('#submitBtn').prop('disabled', true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    $('#rincianList').html(`
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-times-circle me-2"></i>
                            Error loading data. Silakan coba lagi.
                        </div>
                    `);
                    $('#totalNominal').text('Rp 0');
                    $('#submitBtn').prop('disabled', true);
                }
            });
        }

        function loadYearlyData(santriId, tahun) {
            if (!santriId || !tahun) {
                $('#yearlyDataSection').hide();
                return;
            }

            $('#yearlyDataSection').show();
            $('#selectedYear').text(tahun);
            $('#yearlyDataTable').html(`
                <div class="text-center p-4">
                    <div class="loading-spinner"></div>
                    <p class="mb-0 mt-2 text-muted">Memuat data tagihan tahunan...</p>
                </div>
            `);

            // Debug: log URL being called
            const url = "{{ route('tagihan_bulanan.getSantriYearlyData') }}";
            console.log('Calling URL:', url);
            console.log('Data being sent:', {
                santri_id: santriId,
                tahun: tahun
            });

            $.ajax({
                url: url,
                method: 'GET',
                data: $.param({
                    santri_id: santriId,
                    tahun: tahun
                }),
                success: function(response) {
                    console.log('Response received:', response);
                    if (response.success) {
                        displaySummaryCards(response.summary);
                        displayYearlyTable(response.data);
                    } else {
                        $('#yearlyDataTable').html(`
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                ${response.message || 'Tidak ada data tagihan untuk tahun ini.'}
                            </div>
                        `);
                        $('#summaryCards').html('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error Details:');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Status Code:', xhr.status);

                    let errorMessage = 'Error loading yearly data. Silakan coba lagi.';

                    if (xhr.status === 404) {
                        errorMessage = 'Route tidak ditemukan. Pastikan route sudah didefinisikan.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error. Cek log server untuk detail.';
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            errorMessage = 'Response tidak valid dari server.';
                        }
                    }

                    $('#yearlyDataTable').html(`
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-times-circle me-2"></i>
                            ${errorMessage}
                            <br><small class="text-muted">Status: ${xhr.status} - ${status}</small>
                        </div>
                    `);
                    $('#summaryCards').html('');
                }
            });
        }

        function displaySummaryCards(summary) {
            const cards = `
                <div class="summary-card">
                    <h6>Total Tagihan</h6>
                    <div class="value text-primary">${summary.total_tagihan}</div>
                    <div class="subtitle">Bulan</div>
                </div>
                <div class="summary-card">
                    <h6>Lunas</h6>
                    <div class="value text-success">${summary.total_lunas}</div>
                    <div class="subtitle">${summary.lunas_percentage}%</div>
                </div>
                <div class="summary-card">
                    <h6>Belum Lunas</h6>
                    <div class="value text-danger">${summary.total_belum_lunas}</div>
                    <div class="subtitle">${summary.belum_lunas_percentage}%</div>
                </div>
                <div class="summary-card">
                    <h6>Total Nominal</h6>
                    <div class="value text-info">Rp ${number_format(summary.total_nominal)}</div>
                    <div class="subtitle">Tagihan</div>
                </div>
                <div class="summary-card">
                    <h6>Total Dibayar</h6>
                    <div class="value text-success">Rp ${number_format(summary.total_dibayar)}</div>
                    <div class="subtitle">Pembayaran</div>
                </div>
                <div class="summary-card">
                    <h6>Sisa Tagihan</h6>
                    <div class="value text-warning">Rp ${number_format(summary.sisa_tagihan)}</div>
                    <div class="subtitle">Tunggakan</div>
                </div>
            `;
            $('#summaryCards').html(cards);
        }

        function displayYearlyTable(data) {
            if (!data || data.length === 0) {
                $('#yearlyDataTable').html(`
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Belum ada tagihan untuk tahun ini.
                    </div>
                `);
                return;
            }

            let tableHtml = `
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-end">Dibayar</th>
                            <th class="text-end">Sisa</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Tanggal Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach(function(item) {
                const statusClass = getStatusClass(item.status);
                const statusText = getStatusText(item.status);

                tableHtml += `
                    <tr>
                        <td class="fw-semibold">${item.bulan_text}</td>
                        <td class="text-end">Rp ${number_format(item.nominal)}</td>
                        <td class="text-end text-success">Rp ${number_format(item.total_dibayar)}</td>
                        <td class="text-end ${item.sisa > 0 ? 'text-danger' : 'text-success'}">
                            Rp ${number_format(item.sisa)}
                        </td>
                        <td class="text-center">
                            <span class="status-badge status-${item.status}">${statusText}</span>
                        </td>
                        <td class="text-center text-muted">
                            <small>${item.created_at}</small>
                        </td>
                        <td class="text-center">
                            <a href="${item.detail_url}" class="btn btn-sm btn-info text-white"
                               data-bs-toggle="tooltip" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });

            tableHtml += '</tbody></table>';
            $('#yearlyDataTable').html(tableHtml);

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }

        function getStatusClass(status) {
            switch (status) {
                case 'lunas':
                    return 'success';
                case 'dibayar_sebagian':
                    return 'warning';
                case 'belum_lunas':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }

        function getStatusText(status) {
            switch (status) {
                case 'lunas':
                    return 'Lunas';
                case 'dibayar_sebagian':
                    return 'Dibayar Sebagian';
                case 'belum_lunas':
                    return 'Belum Lunas';
                default:
                    return 'Tidak Diketahui';
            }
        }

        function refreshYearlyData() {
            const santriId = $('#santri_id').val();
            const tahun = $('#tahun').val();

            if (santriId && tahun) {
                loadYearlyData(santriId, tahun);
            }
        }

        function number_format(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Prevent double submit
        $('#createForm').on('submit', function() {
            $('#submitBtn').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');
        });
    </script>
@endsection
