@extends('layouts.home')

@section('title_page', 'Generate Tagihan Bulanan Massal')

@section('content')
    <style>
        .month-checkbox {
            display: inline-block;
            margin: 5px;
            padding: 10px 15px;
            border: 2px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .month-checkbox:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .month-checkbox.checked {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .month-checkbox.has-tagihan {
            background-color: #e9ecef;
            color: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
        }

        .santri-selection {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 5px;
        }

        .preview-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        @media (prefers-color-scheme: dark) {
            .month-checkbox {
                border-color: #495057;
            }

            .month-checkbox.checked {
                background-color: #0056b3;
            }

            .santri-selection {
                background-color: #343a40;
                border-color: #495057;
            }
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Generate Tagihan Bulanan Massal</h4>
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

                        <form id="generateBulkForm" method="POST" action="{{ route('tagihan_bulanan.bulkBulanan') }}">
                            @csrf

                            <!-- Year Selection -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label required">Tahun</label>
                                    <select name="tahun" id="tahun" class="form-select" required>
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Month Selection -->
                            <div class="mb-4">
                                <label class="form-label required">Pilih Bulan</label>
                                <div id="monthSelection">
                                    @foreach ($months as $month => $order)
                                        <label class="month-checkbox" data-month="{{ $month }}">
                                            <input type="checkbox" name="bulan[]" value="{{ $month }}"
                                                style="display:none;">
                                            <span>{{ $month }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="selectAllMonths()">
                                        Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="clearAllMonths()">
                                        Hapus Semua
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Options -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Filter Berdasarkan</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="filter_type" id="filterAll"
                                            value="all" checked>
                                        <label class="form-check-label" for="filterAll">
                                            Semua Santri Aktif
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="filter_type" id="filterKelas"
                                            value="kelas">
                                        <label class="form-check-label" for="filterKelas">
                                            Berdasarkan Kelas
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="filter_type" id="filterSantri"
                                            value="santri">
                                        <label class="form-check-label" for="filterSantri">
                                            Pilih Santri Tertentu
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Kelas Selection -->
                            <div id="kelasSelection" style="display:none;" class="mb-4">
                                <label class="form-label">Pilih Kelas</label>
                                <select name="kelas_id" class="form-select">
                                    <option value="">-- Pilih Kelas --</option>
                                    <option value="tanpa_kelas">Tanpa Kelas</option>
                                    @foreach ($kelasList as $kelas)
                                        <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Santri Selection -->
                            <div id="santriSelection" style="display:none;" class="mb-4">
                                <label class="form-label">Pilih Santri <span id="selectedCount">(0 dipilih)</span></label>
                                <div class="mb-2">
                                    <input type="text" class="form-control" id="searchSantri"
                                        placeholder="Cari nama santri atau kelas...">
                                </div>
                                <div class="santri-selection">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllSantri">
                                        <label class="form-check-label" for="selectAllSantri">
                                            <strong>Pilih Semua</strong>
                                        </label>
                                    </div>
                                    <hr>
                                    <div id="santriList">
                                        @foreach ($santris as $santri)
                                            <div class="form-check santri-item"
                                                data-nama="{{ strtolower($santri->nama_santri) }}"
                                                data-kelas="{{ strtolower($santri->nama_kelas_aktif ?? '') }}">
                                                <input class="form-check-input santri-checkbox" type="checkbox"
                                                    name="santri_ids[]" value="{{ $santri->id_santri }}"
                                                    id="santri_{{ $santri->id_santri }}">
                                                <label class="form-check-label" for="santri_{{ $santri->id_santri }}">
                                                    {{ $santri->nama_santri }}
                                                    <small
                                                        class="text-muted">({{ $santri->nama_kelas_aktif ?? 'Tanpa Kelas' }})</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Section -->
                            <div class="preview-section" id="previewSection" style="display:none;">
                                <h5>Preview Generate</h5>
                                <div id="previewContent"></div>
                            </div>

                            <hr>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tagihan_bulanan.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="button" class="btn btn-primary" id="generateBtn"
                                    onclick="confirmGenerate()">
                                    <i class="fas fa-cogs"></i> Generate Tagihan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalTitle">Hasil Generate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="resultModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('tagihan_bulanan.index') }}" class="btn btn-primary">
                        <i class="fas fa-list"></i> Lihat Daftar Tagihan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
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
        $(document).ready(function() {
            // Month checkbox handling
            $('.month-checkbox').on('click', function() {
                const checkbox = $(this).find('input[type="checkbox"]');
                const isChecked = checkbox.prop('checked');

                checkbox.prop('checked', !isChecked);
                $(this).toggleClass('checked');

                updatePreview();
            });

            // Filter type handling
            $('input[name="filter_type"]').on('change', function() {
                $('#kelasSelection').hide();
                $('#santriSelection').hide();

                if ($(this).val() === 'kelas') {
                    $('#kelasSelection').show();
                } else if ($(this).val() === 'santri') {
                    $('#santriSelection').show();
                }

                updatePreview();
            });

            // Santri search
            $('#searchSantri').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();

                $('.santri-item').each(function() {
                    const nama = $(this).data('nama');
                    const kelas = $(this).data('kelas');

                    if (nama.includes(searchTerm) || kelas.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Select all santri
            $('#selectAllSantri').on('change', function() {
                $('.santri-checkbox:visible').prop('checked', $(this).prop('checked'));
                updateSelectedCount();
                updatePreview();
            });

            // Individual santri selection
            $('.santri-checkbox').on('change', function() {
                updateSelectedCount();
                updatePreview();
            });

            // Tahun change
            $('#tahun').on('change', function() {
                checkAvailableMonths();
                updatePreview();
            });

            // Initial check
            checkAvailableMonths();
        });

        function selectAllMonths() {
            $('.month-checkbox:not(.has-tagihan)').each(function() {
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
            $('#selectedCount').text(`(${count} dipilih)`);
        }

        function checkAvailableMonths() {
            const tahun = $('#tahun').val();
            const santriIds = $('.santri-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            $.ajax({
                url: "{{ route('tagihan_bulanan.getAvailableMonths') }}",
                method: 'GET',
                data: {
                    tahun: tahun,
                    santri_ids: santriIds
                },
                success: function(response) {
                    response.months.forEach(function(month) {
                        const monthElement = $(`.month-checkbox[data-month="${month.month}"]`);

                        if (month.hasTagihan) {
                            monthElement.addClass('has-tagihan');
                            monthElement.find('input[type="checkbox"]').prop('checked', false).prop(
                                'disabled', true);
                            monthElement.attr('title', 'Sudah ada tagihan untuk bulan ini');
                        } else {
                            monthElement.removeClass('has-tagihan');
                            monthElement.find('input[type="checkbox"]').prop('disabled', false);
                            monthElement.attr('title', '');
                        }
                    });
                }
            });
        }

        function updatePreview() {
            const selectedMonths = $('input[name="bulan[]"]:checked').length;
            const filterType = $('input[name="filter_type"]:checked').val();
            let targetCount = 0;

            if (filterType === 'all') {
                targetCount = {{ $santris->count() }};
            } else if (filterType === 'kelas' && $('select[name="kelas_id"]').val()) {
                // Estimate based on selected class
                targetCount = 30; // Approximate
            } else if (filterType === 'santri') {
                targetCount = $('.santri-checkbox:checked').length;
            }

            if (selectedMonths > 0 && targetCount > 0) {
                const totalTagihan = selectedMonths * targetCount;
                const html = `
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Bulan dipilih:</strong> ${selectedMonths} bulan</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Estimasi santri:</strong> ${targetCount} santri</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total tagihan akan dibuat:</strong> ${totalTagihan} tagihan</p>
                </div>
            </div>
        `;
                $('#previewContent').html(html);
                $('#previewSection').show();
            } else {
                $('#previewSection').hide();
            }
        }

        function confirmGenerate() {
            const selectedMonths = $('input[name="bulan[]"]:checked').length;

            if (selectedMonths === 0) {
                alert('Pilih minimal satu bulan untuk digenerate!');
                return;
            }

            const filterType = $('input[name="filter_type"]:checked').val();

            if (filterType === 'kelas' && !$('select[name="kelas_id"]').val()) {
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
            const formData = $('#generateBulkForm').serialize();

            $('#loadingModal').modal('show');
            $('#generateBtn').prop('disabled', true);

            $.ajax({
                url: "{{ route('tagihan_bulanan.bulkBulanan') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#loadingModal').modal('hide');

                    if (response.success) {
                        showResult('success', response);
                    } else {
                        showResult('error', response);
                    }
                },
                error: function(xhr) {
                    $('#loadingModal').modal('hide');
                    $('#generateBtn').prop('disabled', false);

                    let message = 'Terjadi kesalahan saat generate tagihan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
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
                title = '<i class="fas fa-check-circle text-success"></i> Generate Berhasil';
                content = `
            <div class="alert alert-success">
                ${data.message}
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <h4>${data.successful}</h4>
                        <p class="text-muted">Berhasil</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h4>${data.failed}</h4>
                        <p class="text-muted">Gagal</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h4>${data.processed}</h4>
                        <p class="text-muted">Total Diproses</p>
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
                title = '<i class="fas fa-exclamation-circle text-danger"></i> Generate Gagal';
                content = `
            <div class="alert alert-danger">
                ${data.message}
            </div>
        `;
            }

            $('#resultModalTitle').html(title);
            $('#resultModalBody').html(content);
            $('#resultModal').modal('show');

            // Reset form
            $('#generateBtn').prop('disabled', false);
            clearAllMonths();
            $('input[name="filter_type"][value="all"]').prop('checked', true).trigger('change');
        }
    </script>
@endsection
