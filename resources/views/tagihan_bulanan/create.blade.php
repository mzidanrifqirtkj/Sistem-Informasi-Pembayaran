@extends('layouts.home')

@section('title_page', 'Buat Tagihan Bulanan Individual')

@section('content')
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

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
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
                    // Debug: tampilkan response di console
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
                        // Support response.rincian as array or object
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
                        // Show success animation
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
