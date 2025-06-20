@extends('layouts.home')

@section('title_page', 'Generate Tagihan Bulanan Massal')

@section('content')
    <style>
        /* Enhanced card styling */
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

        /* ✅ PERBAIKAN MODAL Z-INDEX - Solusi untuk modal di belakang backdrop */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        .modal-dialog {
            z-index: 1060 !important;
        }

        /* Force modal to be on top */
        #resultModal,
        #loadingModal {
            z-index: 9999 !important;
        }

        #resultModal .modal-dialog,
        #loadingModal .modal-dialog {
            z-index: 10000 !important;
        }

        /* Prevent body scroll when modal is open */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0px !important;
        }

        /* Month selection styling */
        .month-checkbox {
            display: inline-block;
            margin: 8px;
            padding: 12px 20px;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fff;
            font-weight: 500;
            min-width: 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
            user-select: none;
        }

        .month-checkbox:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Available month (can be selected) */
        .month-checkbox.available {
            border-color: #198754;
            color: #198754;
            background-color: #d1f2e3;
        }

        .month-checkbox.available:hover {
            border-color: #146c43;
            background-color: #b8e7d1;
        }

        /* Selected month */
        .month-checkbox.checked {
            background-color: #0d6efd !important;
            color: white !important;
            border-color: #0d6efd !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .month-checkbox.checked:hover {
            background-color: #0b5ed7 !important;
            border-color: #0a58ca !important;
        }

        /* Month with existing tagihan */
        .month-checkbox.has-tagihan {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .month-checkbox.has-tagihan::after {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: 3px;
            right: 5px;
            font-size: 0.75rem;
        }

        .month-checkbox input[type="checkbox"] {
            display: none;
        }

        /* Form controls enhancement */
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-label.required::after {
            content: ' *';
            color: #dc3545;
        }

        /* Filter section */
        .filter-card {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        /* Santri selection */
        .santri-selection {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            padding: 1rem;
            border-radius: 10px;
            background-color: #fff;
        }

        .santri-item {
            padding: 0.5rem;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .santri-item:hover {
            background-color: #f8f9fa;
        }

        /* Preview section */
        .preview-section {
            background-color: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        /* Buttons */
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

        .btn-primary:hover:not(:disabled) {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
        }

        /* Loading spinner */
        .month-loading {
            text-align: center;
            padding: 20px;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        /* Alert improvements */
        .alert {
            border-radius: 10px;
            border: none;
        }

        /* Modal improvements */
        .modal-content {
            border-radius: 15px;
            border: none;
            z-index: 10001 !important;
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-radius: 15px 15px 0 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .month-checkbox {
                margin: 5px;
                padding: 10px 15px;
                min-width: 70px;
                font-size: 0.875rem;
            }
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-invoice fa-2x text-success me-3"></i>
                            <div>
                                <h4 class="mb-0">Generate Tagihan Bulanan Massal</h4>
                                <small class="text-muted">Generate tagihan untuk multiple santri dan bulan sekaligus</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Alert Info -->
                        <div class="alert alert-info">
                            <h5 class="alert-heading">
                                <i class="fas fa-info-circle"></i> Informasi Generate Tagihan
                            </h5>
                            <p class="mb-0">
                                Sistem akan mengambil data biaya dari <strong>BiayaSantri</strong> dengan kategori
                                <span class="badge bg-primary">Tambahan</span> dan
                                <span class="badge bg-success">Jalur</span>.
                                Nominal akan dihitung otomatis berdasarkan total biaya yang dialokasikan ke santri.
                            </p>
                        </div>

                        <form id="generateBulkForm" method="POST"
                            action="{{ route('tagihan_bulanan.generateBulkBulanan') }}">
                            @csrf

                            <!-- Year Selection -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label required">Tahun</label>
                                    <select name="tahun" id="tahun" class="form-select" required>
                                        @if (isset($years) && count($years) > 0)
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    {{ $year == date('Y') ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- Filter Options -->
                            <div class="filter-card mb-4">
                                <h5 class="mb-3">
                                    <i class="fas fa-filter me-2"></i>Filter Santri
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="filter_type" id="filterAll"
                                                value="all" checked>
                                            <label class="form-check-label" for="filterAll">
                                                <strong>Semua Santri aktif</strong>
                                                <small class="text-muted d-block">Generate untuk semua santri yang memiliki
                                                    biaya tambahan/jalur</small>
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="filter_type"
                                                id="filterKelas" value="kelas">
                                            <label class="form-check-label" for="filterKelas">
                                                <strong>Berdasarkan Kelas</strong>
                                                <small class="text-muted d-block">Filter santri berdasarkan kelas
                                                    tertentu</small>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="filter_type"
                                                id="filterSantri" value="santri">
                                            <label class="form-check-label" for="filterSantri">
                                                <strong>Pilih Santri Tertentu</strong>
                                                <small class="text-muted d-block">Pilih santri secara manual</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kelas Selection -->
                            <div id="kelasSelection" style="display:none;" class="mb-4">
                                <label class="form-label">Pilih Kelas</label>
                                <select name="kelas_id" id="kelas_id" class="form-select">
                                    <option value="">-- Pilih Kelas --</option>
                                    <option value="tanpa_kelas">Tanpa Kelas</option>
                                    @if (isset($kelasList) && $kelasList->count() > 0)
                                        @foreach ($kelasList as $kelas)
                                            <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Data kelas tidak tersedia</option>
                                    @endif
                                </select>
                            </div>

                            <!-- Santri Selection -->
                            <div id="santriSelection" style="display:none;" class="mb-4">
                                <label class="form-label">Pilih Santri <span class="badge bg-primary" id="selectedCount">0
                                        dipilih</span></label>
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="searchSantri"
                                        placeholder="Cari nama santri atau kelas...">
                                </div>
                                <div class="santri-selection">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="selectAllSantri">
                                        <label class="form-check-label" for="selectAllSantri">
                                            <strong>Pilih Semua</strong>
                                        </label>
                                    </div>
                                    <hr>
                                    <div id="santriList">
                                        @if (isset($santris) && $santris->count() > 0)
                                            @foreach ($santris as $santri)
                                                <div class="form-check santri-item"
                                                    data-nama="{{ strtolower($santri->nama_santri) }}"
                                                    data-kelas="{{ strtolower($santri->nama_kelas_aktif ?? '') }}">
                                                    <input class="form-check-input santri-checkbox" type="checkbox"
                                                        name="santri_ids[]" value="{{ $santri->id_santri }}"
                                                        id="santri_{{ $santri->id_santri }}">
                                                    <label class="form-check-label" for="santri_{{ $santri->id_santri }}">
                                                        {{ $santri->nama_santri }}
                                                        <small class="text-muted">
                                                            ({{ $santri->nis }} -
                                                            {{ $santri->nama_kelas_aktif ?? 'Tanpa Kelas' }})
                                                        </small>
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-muted">Tidak ada santri dengan biaya tambahan/jalur</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Month Selection -->
                            <div class="mb-4">
                                <label class="form-label required">Pilih Bulan</label>
                                <div id="monthLoadingIndicator" class="month-loading" style="display:none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 mb-0 text-muted">Mengecek ketersediaan bulan...</p>
                                </div>
                                <div id="monthSelection">
                                    @if (isset($months) && count($months) > 0)
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
                                        @foreach ($months as $month => $order)
                                            <label class="month-checkbox available" data-month="{{ $month }}">
                                                <input type="checkbox" name="bulan[]" value="{{ $month }}">
                                                <span>{{ substr($monthNames[$month] ?? $month, 0, 3) }}</span>
                                            </label>
                                        @endforeach
                                    @else
                                        <div class="text-muted">Data bulan tidak tersedia</div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-success"
                                        onclick="selectAvailableMonths()">
                                        <i class="fas fa-check-square me-1"></i>Pilih Bulan Tersedia
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="clearAllMonths()">
                                        <i class="fas fa-times-square me-1"></i>Hapus Semua
                                    </button>
                                </div>
                            </div>

                            <!-- Preview Section -->
                            <div class="preview-section" id="previewSection" style="display:none;">
                                <h5><i class="fas fa-eye me-2"></i>Preview Generate</h5>
                                <div id="previewContent"></div>
                            </div>

                            <hr>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('tagihan_bulanan.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <div>
                                    <!-- Test Button untuk Debug -->
                                    <button type="button" class="btn btn-warning me-2" onclick="testFunction()">
                                        <i class="fas fa-bug me-1"></i>Test
                                    </button>
                                    <button type="button" class="btn btn-primary" id="generateBtn">
                                        <i class="fas fa-cogs me-2"></i>Generate Tagihan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalTitle">Hasil Generate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="resultModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-primary" onclick="redirectToIndex()">
                        <i class="fas fa-list me-1"></i>Lihat Daftar Tagihan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0">Sedang memproses tagihan...</p>
                    <p class="mb-0"><small class="text-muted">Harap tunggu, jangan tutup halaman ini</small></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let isProcessing = false;

        // ✅ TEST FUNCTION untuk debugging
        function testFunction() {
            console.log('✅ Test function works!');
            alert('Test button works! jQuery: ' + (typeof $ !== 'undefined'));

            if (typeof confirmGenerate === 'function') {
                console.log('✅ confirmGenerate function found');
                confirmGenerate();
            } else {
                console.log('❌ confirmGenerate function NOT found');
                alert('confirmGenerate function not loaded');
            }
        }

        $(document).ready(function() {
            console.log('🚀 Document ready - initializing...');

            // ✅ MULTIPLE EVENT HANDLERS untuk button Generate
            $('#generateBtn').on('click', function(e) {
                e.preventDefault();
                console.log('🎯 Generate clicked via jQuery!');
                confirmGenerate();
            });

            // Backup vanilla JS event
            const generateBtn = document.getElementById('generateBtn');
            if (generateBtn) {
                generateBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('🎯 Generate clicked via vanilla JS!');
                    confirmGenerate();
                });
            }

            // ✅ MODAL EVENT HANDLERS untuk Bootstrap 5
            $('#resultModal, #loadingModal').on('show.bs.modal', function() {
                console.log('📱 Modal showing...');
                $(this).css('z-index', '9999');
                $('.modal-backdrop').css('z-index', '9998');
                $('body').addClass('modal-open');
            });

            $('#resultModal, #loadingModal').on('hidden.bs.modal', function() {
                console.log('📱 Modal hidden...');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            });

            // Month checkbox handling
            $(document).on('click', '.month-checkbox', function(e) {
                e.preventDefault();

                if ($(this).hasClass('has-tagihan')) {
                    console.log('📅 Month has tagihan, cannot select');
                    return false;
                }

                const checkbox = $(this).find('input[type="checkbox"]');
                const isChecked = checkbox.prop('checked');

                checkbox.prop('checked', !isChecked);

                if (!isChecked) {
                    $(this).addClass('checked');
                } else {
                    $(this).removeClass('checked');
                }

                updatePreview();
                console.log('📅 Month clicked:', $(this).data('month'), 'Checked:', !isChecked);
            });

            // Filter type handling
            $('input[name="filter_type"]').on('change', function() {
                console.log('🔍 Filter changed to:', $(this).val());

                $('#kelasSelection').hide();
                $('#santriSelection').hide();

                if ($(this).val() === 'kelas') {
                    $('#kelasSelection').show();
                } else if ($(this).val() === 'santri') {
                    $('#santriSelection').show();
                }

                checkAvailableMonths();
                updatePreview();
            });

            // When tahun, kelas, or santri selection changes
            $('#tahun, #kelas_id').on('change', function() {
                console.log('📊 Selection changed:', $(this).attr('id'), $(this).val());
                checkAvailableMonths();
                updatePreview();
            });

            // Santri search
            $('#searchSantri').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();

                $('.santri-item').each(function() {
                    const nama = $(this).data('nama') || '';
                    const kelas = $(this).data('kelas') || '';

                    if (nama.includes(searchTerm) || kelas.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Select all santri
            $('#selectAllSantri').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.santri-checkbox:visible').prop('checked', isChecked);
                updateSelectedCount();
                checkAvailableMonths();
                updatePreview();
            });

            // Individual santri selection
            $(document).on('change', '.santri-checkbox', function() {
                updateSelectedCount();
                checkAvailableMonths();
                updatePreview();
            });

            // Initial check
            checkAvailableMonths();
            console.log('✅ Initialization complete');
        });

        function confirmGenerate() {
            console.log('🎯 confirmGenerate called!');

            if (isProcessing) {
                console.log('⚠️ Already processing...');
                return;
            }

            const selectedMonths = $('input[name="bulan[]"]:checked').length;
            console.log('📅 Selected months:', selectedMonths);

            if (selectedMonths === 0) {
                alert('Pilih minimal satu bulan untuk digenerate!');
                return;
            }

            const filterType = $('input[name="filter_type"]:checked').val();

            if (filterType === 'kelas' && !$('#kelas_id').val()) {
                alert('Pilih kelas terlebih dahulu!');
                return;
            }

            if (filterType === 'santri' && $('.santri-checkbox:checked').length === 0) {
                alert('Pilih minimal satu santri!');
                return;
            }

            if (confirm('Apakah Anda yakin ingin generate tagihan?')) {
                generateTagihan();
            }
        }

        function generateTagihan() {
            if (isProcessing) return;

            console.log('🚀 Starting generate...');
            isProcessing = true;

            const formData = new FormData($('#generateBulkForm')[0]);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            // Show loading modal untuk Bootstrap 5
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();

            setTimeout(function() {
                $('#loadingModal').css('z-index', '9999');
                $('.modal-backdrop').css('z-index', '9998');
            }, 100);

            $('#generateBtn').prop('disabled', true);

            $.ajax({
                url: "{{ route('tagihan_bulanan.generateBulkBulanan') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('✅ Success:', response);
                    loadingModal.hide();

                    if (response.success) {
                        showResult('success', response);
                    } else {
                        showResult('error', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error:', xhr, status, error);
                    loadingModal.hide();
                    $('#generateBtn').prop('disabled', false);
                    isProcessing = false;

                    let message = 'Terjadi kesalahan saat generate tagihan';

                    if (xhr.status === 419) {
                        message = 'CSRF Token kedaluwarsa. Refresh halaman dan coba lagi.';
                    } else if (xhr.status === 422) {
                        message = 'Data tidak valid. Periksa input Anda.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    showResult('error', {
                        message: message
                    });
                }
            });
        }

        function showResult(type, data) {
            let title = '';
            let content = '';

            if (type === 'success') {
                title = '<i class="fas fa-check-circle text-success me-2"></i>Generate Berhasil';
                content = `
                    <div class="alert alert-success">
                        <i class="fas fa-check me-2"></i>${data.message}
                    </div>
                    <div class="row text-center mt-4">
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h3 class="text-success mb-0">${data.successful}</h3>
                                    <p class="mb-0">Berhasil</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <h3 class="text-danger mb-0">${data.failed}</h3>
                                    <p class="mb-0">Gagal</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h3 class="text-info mb-0">${data.processed}</h3>
                                    <p class="mb-0">Total Diproses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                if (data.errors && data.errors.length > 0) {
                    content += `
                        <hr>
                        <h6>Detail Error:</h6>
                        <div class="alert alert-warning" style="max-height: 200px; overflow-y: auto;">
                           <ul class="mb-0">
                               ${data.errors.map(error => `<li>${error}</li>`).join('')}
                           </ul>
                       </div>
                   `;
                }
            } else {
                title = '<i class="fas fa-exclamation-circle text-danger me-2"></i>Generate Gagal';
                content = `
                   <div class="alert alert-danger">
                       <i class="fas fa-times me-2"></i>${data.message}
                   </div>
               `;
            }

            $('#resultModalTitle').html(title);
            $('#resultModalBody').html(content);

            // Show result modal untuk Bootstrap 5
            const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
            resultModal.show();

            setTimeout(function() {
                $('#resultModal').css('z-index', '9999');
                $('.modal-backdrop').css('z-index', '9998');
            }, 100);

            $('#generateBtn').prop('disabled', false);
            isProcessing = false;
        }

        function selectAvailableMonths() {
            $('.month-checkbox.available:not(.has-tagihan)').each(function() {
                $(this).addClass('checked');
                $(this).find('input[type="checkbox"]').prop('checked', true);
            });
            updatePreview();
        }

        function clearAllMonths() {
            $('.month-checkbox').removeClass('checked');
            $('.month-checkbox input[type="checkbox"]').prop('checked', false);
            updatePreview();
        }

        function updateSelectedCount() {
            const count = $('.santri-checkbox:checked').length;
            $('#selectedCount').text(`${count} dipilih`);
        }

        function checkAvailableMonths() {
            const tahun = $('#tahun').val();
            const filterType = $('input[name="filter_type"]:checked').val();
            let santriIds = [];

            if (filterType === 'santri') {
                santriIds = $('.santri-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();
            }

            $('#monthLoadingIndicator').show();
            $('#monthSelection').css('opacity', '0.5');

            $.ajax({
                url: "{{ route('tagihan_bulanan.getAvailableMonths') }}",
                method: 'GET',
                data: {
                    tahun: tahun,
                    santri_ids: santriIds
                },
                success: function(response) {
                    if (response.months) {
                        response.months.forEach(function(month) {
                            const monthElement = $(`.month-checkbox[data-month="${month.month}"]`);

                            if (month.hasTagihan) {
                                monthElement.removeClass('available checked').addClass('has-tagihan');
                                monthElement.find('input[type="checkbox"]').prop('checked', false).prop(
                                    'disabled', true);
                                monthElement.attr('title', 'Sudah ada tagihan untuk bulan ini');
                            } else {
                                monthElement.removeClass('has-tagihan').addClass('available');
                                monthElement.find('input[type="checkbox"]').prop('disabled', false);
                                monthElement.attr('title', 'Tersedia untuk generate');
                            }
                        });
                    }

                    $('#monthLoadingIndicator').hide();
                    $('#monthSelection').css('opacity', '1');
                    updatePreview();
                },
                error: function(xhr) {
                    console.error('Error checking months:', xhr);
                    $('#monthLoadingIndicator').hide();
                    $('#monthSelection').css('opacity', '1');
                    $('.month-checkbox').removeClass('has-tagihan').addClass('available');
                    $('.month-checkbox input[type="checkbox"]').prop('disabled', false);
                }
            });
        }

        function updatePreview() {
            const selectedMonths = $('input[name="bulan[]"]:checked').length;
            const filterType = $('input[name="filter_type"]:checked').val();
            let targetCount = 0;
            let filterText = '';

            if (filterType === 'all') {
                targetCount = {{ isset($santris) ? $santris->count() : 0 }};
                filterText = 'Semua santri aktif';
            } else if (filterType === 'kelas' && $('#kelas_id').val()) {
                targetCount = 30;
                filterText = 'Kelas: ' + $('#kelas_id option:selected').text();
            } else if (filterType === 'santri') {
                targetCount = $('.santri-checkbox:checked').length;
                filterText = targetCount + ' santri dipilih';
            }

            if (selectedMonths > 0 && targetCount > 0) {
                const totalTagihan = selectedMonths * targetCount;
                const html = `
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-primary mb-0">${selectedMonths}</h5>
                                <small class="text-muted">Bulan dipilih</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-success mb-0">${targetCount}</h5>
                                <small class="text-muted">${filterText}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-info mb-0">${totalTagihan}</h5>
                                <small class="text-muted">Total tagihan akan dibuat</small>
                            </div>
                        </div>
                    </div>
                `;
                $('#previewContent').html(html);
                $('#previewSection').show();
            } else {
                $('#previewSection').hide();
            }
        }

        function redirectToIndex() {
            window.location.href = "{{ route('tagihan_bulanan.index') }}";
        }

        // ✅ TESTING FUNCTIONS
        window.testGenerate = function() {
            console.log('🧪 Testing generate...');
            $('input[name="bulan[]"][value="Jan"]').prop('checked', true);
            $('.month-checkbox[data-month="Jan"]').addClass('checked');
            confirmGenerate();
        };

        window.debugModal = function() {
            console.log('🔍 Modal Debug:');
            console.log('- Result Modal Z-Index:', $('#resultModal').css('z-index'));
            console.log('- Backdrop Z-Index:', $('.modal-backdrop').css('z-index'));

            $('#resultModal').css('z-index', '99999');
            $('.modal-backdrop').css('z-index', '99998');
        };
    </script>
@endsection
