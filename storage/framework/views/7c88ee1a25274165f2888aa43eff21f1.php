<?php $__env->startSection('title_page', 'Pembayaran Massal'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4>Pembayaran Massal</h4>
                    <div class="card-header-action">
                        <a href="<?php echo e(route('pembayaran.bulk.import')); ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-upload"></i> Import Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Step 1: Pilih Tipe Tagihan -->
                    <div class="form-group">
                        <label class="form-label"><strong>Step 1: Pilih Tipe Tagihan</strong></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tagihan_type" id="tipeBulanan"
                                    value="bulanan" <?php echo e($tagihanType === 'bulanan' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="tipeBulanan">
                                    <i class="fas fa-calendar-alt"></i> Tagihan Bulanan
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tagihan_type" id="tipeTerjadwal"
                                    value="terjadwal" <?php echo e($tagihanType === 'terjadwal' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="tipeTerjadwal">
                                    <i class="fas fa-clock"></i> Tagihan Terjadwal
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Filter dan Pilih Santri -->
                    <div id="santriSection" style="<?php echo e($tagihanType ? '' : 'display:none;'); ?>">
                        <hr>
                        <label class="form-label"><strong>Step 2: Filter dan Pilih Santri</strong></label>

                        <!-- Filter Controls -->
                        <form id="filterForm" method="GET" action="<?php echo e(route('pembayaran.bulk.index')); ?>">
                            <input type="hidden" name="tagihan_type" value="<?php echo e($tagihanType); ?>">

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select name="kategori" class="form-control select2 filter-controls">
                                        <option value="">Semua Kategori</option>
                                        <?php $__currentLoopData = $kategoriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($kat->id_kategori_biaya); ?>"
                                                <?php echo e($kategori == $kat->id_kategori_biaya ? 'selected' : ''); ?>>
                                                <?php echo e($kat->nama_kategori); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="kelas" class="form-control select2 filter-controls">
                                        <option value="">Semua Kelas</option>
                                        <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelasItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($kelasItem->id_kelas); ?>"
                                                <?php echo e($kelas == $kelasItem->id_kelas ? 'selected' : ''); ?>>
                                                <?php echo e($kelasItem->nama_kelas); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="jenis_kelamin" class="form-control select2 filter-controls">
                                        <option value="">Semua Jenis Kelamin</option>
                                        <option value="L" <?php echo e($jenisKelamin === 'L' ? 'selected' : ''); ?>>Laki-laki
                                        </option>
                                        <option value="P" <?php echo e($jenisKelamin === 'P' ? 'selected' : ''); ?>>Perempuan
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="per_page" class="form-control filter-controls">
                                        <option value="20" <?php echo e($perPage == 20 ? 'selected' : ''); ?>>20 per halaman
                                        </option>
                                        <option value="50" <?php echo e($perPage == 50 ? 'selected' : ''); ?>>50 per halaman
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>

                        <!-- Santri Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                                    <label class="form-check-label" for="selectAllCheckbox">
                                        Pilih Semua di Halaman Ini
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <small class="text-muted">
                                    Terpilih: <span id="selectedCount" class="font-weight-bold">0</span> santri
                                </small>
                            </div>
                        </div>

                        <!-- Santri Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllHeader" style="display:none;">
                                        </th>
                                        <th>NIS</th>
                                        <th>Nama</th>
                                        <th>Kategori</th>
                                        <th>Kelas</th>
                                        <th>JK</th>
                                        <th class="text-right">Total Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="santri-row" data-kategori="<?php echo e($santri->kategori_biaya_id_for_filter); ?>"
                                            data-kelas="<?php echo e($santri->kelas_aktif_id ?? ''); ?>"
                                            data-jenis-kelamin="<?php echo e($santri->jenis_kelamin); ?>"
                                            data-total-tagihan="<?php echo e($santri->total_tagihan); ?>">
                                            <td>
                                                <input type="checkbox" class="santri-checkbox" name="santri_ids[]"
                                                    value="<?php echo e($santri->id_santri); ?>">
                                            </td>
                                            <td class="santri-nis"><?php echo e($santri->nis); ?></td>
                                            <td class="santri-name"><?php echo e($santri->nama_santri); ?></td>
                                            <td><?php echo e($santri->kategori_biaya_display->nama_kategori ?? '-'); ?></td>
                                            <td><?php echo e($santri->kelas_aktif->nama_kelas ?? '-'); ?></td>
                                            <td>
                                                <span
                                                    class="badge badge-<?php echo e($santri->jenis_kelamin === 'L' ? 'primary' : 'danger'); ?>">
                                                    <?php echo e($santri->jenis_kelamin === 'L' ? 'L' : 'P'); ?>

                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <strong><?php echo e(format_rupiah($santri->total_tagihan)); ?></strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle"></i>
                                                <?php if($tagihanType === 'bulanan'): ?>
                                                    Tidak ada santri dengan tagihan bulanan yang belum lunas
                                                <?php else: ?>
                                                    Tidak ada santri dengan tagihan terjadwal yang belum lunas
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if($santris->hasPages()): ?>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">
                                        Menampilkan <?php echo e($santris->firstItem()); ?> sampai <?php echo e($santris->lastItem()); ?>

                                        dari <?php echo e($santris->total()); ?> santri
                                    </small>
                                </div>
                                <div>
                                    <?php echo e($santris->appends(request()->query())->links()); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Payment Form -->
        <div class="col-lg-4">
            <div id="paymentSection" style="display:none;">
                <div class="card sticky-top">
                    <div class="card-header">
                        <h5><i class="fas fa-money-bill-wave"></i> Form Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form id="bulkPaymentForm">
                            <?php echo csrf_field(); ?>

                            <div class="form-group">
                                <label>Tipe Tagihan</label>
                                <input type="text" class="form-control" readonly
                                    value="<?php echo e($tagihanType === 'bulanan' ? 'Tagihan Bulanan' : 'Tagihan Terjadwal'); ?>">
                            </div>

                            <div class="form-group">
                                <label>Jumlah Santri Terpilih</label>
                                <input type="text" class="form-control" readonly id="selectedCountDisplay"
                                    value="0 santri">
                            </div>

                            <div class="form-group">
                                <label for="nominalPerSantri">Nominal per Santri <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="nominal_per_santri"
                                    id="nominalPerSantri" placeholder="Masukkan nominal..." min="1"
                                    step="1000">
                                <small class="text-muted">Nominal yang sama akan diberikan ke setiap santri</small>

                                <!-- Overpayment Warning -->
                                <div id="overpaymentWarning" class="alert alert-warning mt-2" style="display:none;">
                                    <!-- Warning content will be populated by JavaScript -->
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-eye"></i> Preview Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">
                        <i class="fas fa-eye"></i> Preview Pembayaran Massal
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tipe Tagihan:</strong><br>
                            <span id="previewTipeTagihan">-</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Jumlah Santri:</strong><br>
                            <span id="previewJumlahSantri">-</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Total Keseluruhan:</strong><br>
                            <span id="previewTotalKeseluruhan" class="text-success font-weight-bold">-</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>NIS</th>
                                    <th>Nama Santri</th>
                                    <th class="text-right">Total Tagihan</th>
                                    <th class="text-right">Nominal Bayar</th>
                                    <th class="text-right">Alokasi</th>
                                </tr>
                            </thead>
                            <tbody id="previewTableBody">
                                <!-- Content will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="editPayment">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="button" class="btn btn-success" id="processPayment">
                        <i class="fas fa-check"></i> Ya, Proses
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        // Revised JavaScript for Enhanced Bulk Payment Design
        // with Pagination, All Tagihan Support, and Preview Modal

        $(document).ready(function() {
            initializeComponents();
            bindEvents();
            updateSelectedCount();
        });

        function initializeComponents() {
            // Initialize Select2 components
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Set initial state
            toggleSantriList();
            updateSelectedCount();

            // Auto-save form data
            loadSavedFormData();
        }

        function bindEvents() {
            // Tagihan type change
            $('input[name="tagihan_type"]').on('change', function() {
                saveFormData();
                filterSantri();
                toggleSantriList();
                updateSelectedCount();
                clearWarnings();
            });

            // Filter changes
            $('select[name="kategori"], select[name="kelas"], select[name="jenis_kelamin"]').on('change', function() {
                saveFormData();
                filterSantri();
            });

            // Per page change
            $('select[name="per_page"]').on('change', function() {
                saveFormData();
                $('#filterForm').submit();
            });

            // Santri selection
            $(document).on('change', '.santri-checkbox', function() {
                saveFormData();
                updateSelectedCount();
                validateOverpayment();
            });

            // Select all functionality
            $('#selectAllCheckbox').on('change', function() {
                const checked = $(this).prop('checked');
                $('.santri-row:visible .santri-checkbox').prop('checked', checked);
                saveFormData();
                updateSelectedCount();
                validateOverpayment();
            });

            // Nominal input
            $('input[name="nominal_per_santri"]').on('input', function() {
                saveFormData();
                validateOverpayment();
            });

            // Form submission
            $('#bulkPaymentForm').on('submit', function(e) {
                e.preventDefault();
                showPreviewModal();
            });

            // Preview modal buttons
            $(document).on('click', '#processPayment', processPayment);
            $(document).on('click', '#editPayment', function() {
                $('#previewModal').modal('hide');
            });
        }

        function filterSantri() {
            const tagihanType = $('input[name="tagihan_type"]:checked').val();
            const kategori = $('select[name="kategori"]').val();
            const kelas = $('select[name="kelas"]').val();
            const jenisKelamin = $('select[name="jenis_kelamin"]').val();

            // Reload page with filters
            const params = new URLSearchParams(window.location.search);
            params.set('tagihan_type', tagihanType);
            if (kategori) params.set('kategori', kategori);
            else params.delete('kategori');
            if (kelas) params.set('kelas', kelas);
            else params.delete('kelas');
            if (jenisKelamin) params.set('jenis_kelamin', jenisKelamin);
            else params.delete('jenis_kelamin');

            window.location.href = window.location.pathname + '?' + params.toString();
        }

        function toggleSantriList() {
            const tagihanType = $('input[name="tagihan_type"]:checked').val();

            if (tagihanType) {
                $('#santriSection').show();
                $('.filter-controls').prop('disabled', false);
            } else {
                $('#santriSection').hide();
                $('.filter-controls').prop('disabled', true);
            }
        }

        function updateSelectedCount() {
            const selectedCount = $('.santri-checkbox:checked').length;
            $('#selectedCount').text(selectedCount);
            $('#selectedCountDisplay').val(selectedCount + ' santri');

            const visibleCount = $('.santri-row:visible .santri-checkbox').length;
            const checkedCount = $('.santri-row:visible .santri-checkbox:checked').length;

            if (visibleCount > 0 && visibleCount === checkedCount) {
                $('#selectAllCheckbox').prop('checked', true);
            } else {
                $('#selectAllCheckbox').prop('checked', false);
            }

            // Update payment form visibility
            if (selectedCount > 0) {
                $('#paymentSection').show();
            } else {
                $('#paymentSection').hide();
            }
        }

        function validateOverpayment() {
            const nominal = parseFloat($('input[name="nominal_per_santri"]').val()) || 0;
            const selectedSantri = $('.santri-checkbox:checked');

            $('#overpaymentWarning').hide();

            if (nominal > 0 && selectedSantri.length > 0) {
                selectedSantri.each(function() {
                    const row = $(this).closest('.santri-row');
                    const totalTagihan = parseFloat(row.data('total-tagihan')) || 0;

                    if (nominal > totalTagihan) {
                        const kelebihan = nominal - totalTagihan;
                        const namaSantri = row.find('.santri-name').text();

                        $('#overpaymentWarning').html(
                            '<i class="fas fa-exclamation-triangle"></i> ' +
                            'Peringatan: Nominal melebihi tagihan ' + namaSantri + ' sebesar Rp ' +
                            number_format(kelebihan) + '. Kelebihan akan dialokasikan ke tagihan berikutnya.'
                        ).show();

                        return false; // Break loop on first overpayment
                    }
                });
            }
        }

        function showPreviewModal() {
            const selectedSantri = $('.santri-checkbox:checked');
            const nominal = parseFloat($('input[name="nominal_per_santri"]').val()) || 0;
            const tagihanType = $('input[name="tagihan_type"]:checked').val();

            // Validation
            if (selectedSantri.length === 0) {
                Swal.fire('Peringatan', 'Pilih santri yang akan dibayar', 'warning');
                return;
            }

            if (nominal <= 0) {
                Swal.fire('Peringatan', 'Masukkan nominal pembayaran', 'warning');
                return;
            }

            // Build preview content
            let previewContent = '';
            let totalKeseluruhan = 0;

            selectedSantri.each(function() {
                const row = $(this).closest('.santri-row');
                const santriId = $(this).val();
                const namaSantri = row.find('.santri-name').text();
                const nisSantri = row.find('.santri-nis').text();
                const totalTagihan = parseFloat(row.data('total-tagihan')) || 0;

                const nominalBayar = Math.min(nominal, totalTagihan);
                const kelebihan = nominal > totalTagihan ? nominal - totalTagihan : 0;

                previewContent += `
            <tr>
                <td>${nisSantri}</td>
                <td>${namaSantri}</td>
                <td class="text-right">Rp ${number_format(totalTagihan)}</td>
                <td class="text-right">Rp ${number_format(nominal)}</td>
                <td class="text-right">
                    Rp ${number_format(nominalBayar)}
                    ${kelebihan > 0 ? '<br><small class="text-info">+ Rp ' + number_format(kelebihan) + ' dialokasikan</small>' : ''}
                </td>
            </tr>
        `;

                totalKeseluruhan += nominal;
            });

            // Populate modal
            $('#previewTableBody').html(previewContent);
            $('#previewTotalKeseluruhan').text('Rp ' + number_format(totalKeseluruhan));
            $('#previewJumlahSantri').text(selectedSantri.length + ' santri');
            $('#previewTipeTagihan').text(tagihanType === 'bulanan' ? 'Bulanan' : 'Terjadwal');

            // Show modal
            $('#previewModal').modal('show');
        }

        function processPayment() {
            const selectedSantri = $('.santri-checkbox:checked');
            const nominal = parseFloat($('input[name="nominal_per_santri"]').val()) || 0;
            const tagihanType = $('input[name="tagihan_type"]:checked').val();

            // Prepare data
            const santriIds = [];
            selectedSantri.each(function() {
                santriIds.push($(this).val());
            });

            const formData = {
                tagihan_type: tagihanType,
                santri_ids: santriIds,
                nominal_per_santri: nominal,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Show loading
            $('#processPayment').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

            // Submit
            $.ajax({
                url: '<?php echo e(route('pembayaran.bulk.process')); ?>',
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#previewModal').modal('hide');

                    if (response.success) {
                        clearSavedFormData();
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        }).then(() => {
                            if (response.redirect_url) {
                                window.location.href = response.redirect_url;
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function(xhr) {
                    $('#previewModal').modal('hide');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Terjadi kesalahan validasi:\n';
                        Object.values(errors).forEach(error => {
                            errorMessage += '• ' + error[0] + '\n';
                        });
                        Swal.fire('Error Validasi', errorMessage, 'error');
                    } else {
                        Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                    }
                },
                complete: function() {
                    $('#processPayment').prop('disabled', false).html(
                        '<i class="fas fa-check"></i> Ya, Proses');
                }
            });
        }

        function clearWarnings() {
            $('#overpaymentWarning').hide();
        }

        function saveFormData() {
            const formData = {
                tagihan_type: $('input[name="tagihan_type"]:checked').val(),
                filter_kategori: $('select[name="kategori"]').val(),
                filter_kelas: $('select[name="kelas"]').val(),
                filter_jenis_kelamin: $('select[name="jenis_kelamin"]').val(),
                nominal_per_santri: $('input[name="nominal_per_santri"]').val(),
                selected_santri: $('.santri-checkbox:checked').map(function() {
                    return $(this).val();
                }).get()
            };

            sessionStorage.setItem('bulkPaymentForm', JSON.stringify(formData));
        }

        function loadSavedFormData() {
            const saved = sessionStorage.getItem('bulkPaymentForm');
            if (saved) {
                const formData = JSON.parse(saved);

                // Restore selections
                if (formData.tagihan_type) {
                    $('input[name="tagihan_type"][value="' + formData.tagihan_type + '"]').prop('checked', true);
                }
                if (formData.filter_kategori) {
                    $('select[name="kategori"]').val(formData.filter_kategori).trigger('change');
                }
                if (formData.filter_kelas) {
                    $('select[name="kelas"]').val(formData.filter_kelas).trigger('change');
                }
                if (formData.filter_jenis_kelamin) {
                    $('select[name="jenis_kelamin"]').val(formData.filter_jenis_kelamin).trigger('change');
                }
                if (formData.nominal_per_santri) {
                    $('input[name="nominal_per_santri"]').val(formData.nominal_per_santri);
                }
                if (formData.selected_santri && formData.selected_santri.length > 0) {
                    formData.selected_santri.forEach(function(santriId) {
                        $('input[name="santri_ids[]"][value="' + santriId + '"]').prop('checked', true);
                    });
                }
            }
        }

        function clearSavedFormData() {
            sessionStorage.removeItem('bulkPaymentForm');
        }

        function number_format(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/pembayaran/bulk.blade.php ENDPATH**/ ?>