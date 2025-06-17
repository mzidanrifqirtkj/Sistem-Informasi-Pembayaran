<?php $__env->startSection('title_page', 'Generate Tagihan Massal'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Generate Tagihan Massal</h2>
                    <a href="<?php echo e(route('tagihan_terjadwal.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Warning Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <h5 class="alert-heading">
                        <i class="fas fa-info-circle"></i> Informasi Generate Tagihan Massal
                    </h5>
                    <p class="mb-2">Generate tagihan massal akan membuat tagihan untuk <strong>semua santri</strong> yang
                        memiliki alokasi biaya sesuai filter yang dipilih.</p>
                    <p class="mb-2">
                        <strong>Kategori yang tersedia:</strong>
                        <span class="badge badge-primary ml-1">Tahunan</span>
                        <span class="badge badge-success ml-1">Insidental</span>
                    </p>
                    <p class="mb-2">
                        <strong>Pilihan Filter:</strong><br>
                        <small>
                            • <strong>Kategori Biaya:</strong> Generate untuk semua jenis biaya dalam kategori<br>
                            • <strong>Jenis Biaya Tertentu:</strong> Generate hanya untuk jenis biaya yang dipilih<br>
                            • Pilih minimal salah satu filter
                        </small>
                    </p>
                    <hr>
                    <p class="mb-0">
                        <small>
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            Tagihan yang sudah ada dengan kombinasi yang sama tidak akan dibuat ulang.
                        </small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group"></i> Form Generate Tagihan Massal
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="generateBulkForm" method="POST"
                            action="<?php echo e(route('tagihan_terjadwal.generateBulkTerjadwal')); ?>">
                            <?php echo csrf_field(); ?>

                            <!-- Filter Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <h6 class="mb-2">
                                            <i class="fas fa-filter"></i> Pilihan Filter (Pilih minimal salah satu)
                                        </h6>
                                        <small>Anda dapat memilih berdasarkan kategori biaya (semua jenis) atau jenis biaya
                                            tertentu.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Kategori Biaya Status -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kategori_biaya_status">Filter Kategori Biaya</label>
                                        <select name="kategori_biaya_status" id="kategori_biaya_status"
                                            class="form-control">
                                            <option value="">-- Semua Kategori --</option>
                                            <?php $__currentLoopData = $kategoriBiayaStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($status); ?>"
                                                    <?php echo e(old('kategori_biaya_status') == $status ? 'selected' : ''); ?>>
                                                    <?php echo e(ucwords(str_replace('_', ' ', $status))); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <small class="form-text text-muted">
                                            Generate untuk semua jenis biaya dalam kategori ini.
                                        </small>
                                    </div>
                                </div>

                                <!-- Jenis Biaya Tertentu -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jenis_biaya_id">Filter Jenis Biaya Tertentu</label>
                                        <select name="jenis_biaya_id" id="jenis_biaya_id" class="form-control">
                                            <option value="">-- Semua Jenis Biaya --</option>
                                            <?php $__currentLoopData = $jenisBiayaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jenisBiaya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($jenisBiaya->id_kategori_biaya); ?>"
                                                    <?php echo e(old('jenis_biaya_id') == $jenisBiaya->id_kategori_biaya ? 'selected' : ''); ?>>
                                                    <?php echo e($jenisBiaya->nama_kategori); ?>

                                                    <small class="text-muted">[<?php echo e(ucfirst($jenisBiaya->status)); ?>]</small>
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <small class="form-text text-muted">
                                            Generate hanya untuk jenis biaya ini saja.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Tahun -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tahun" class="required">Tahun</label>
                                        <select name="tahun" id="tahun" class="form-control" required>
                                            <option value="">-- Pilih Tahun --</option>
                                            <?php for($year = $currentYear - 2; $year <= $currentYear + 2; $year++): ?>
                                                <option value="<?php echo e($year); ?>"
                                                    <?php echo e(old('tahun', $currentYear) == $year ? 'selected' : ''); ?>>
                                                    <?php echo e($year); ?>

                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        <small class="form-text text-muted">
                                            Tahun untuk tagihan yang akan dibuat.
                                        </small>
                                    </div>
                                </div>

                                <!-- Tahun Ajar -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tahun_ajar_id">Tahun Ajar (Opsional)</label>
                                        <select name="tahun_ajar_id" id="tahun_ajar_id" class="form-control">
                                            <option value="">-- Tidak Ada --</option>
                                            <?php $__currentLoopData = $tahunAjars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahunAjar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($tahunAjar->id_tahun_ajar); ?>"
                                                    <?php echo e(old('tahun_ajar_id') == $tahunAjar->id_tahun_ajar ? 'selected' : ''); ?>>
                                                    <?php echo e($tahunAjar->tahun_ajar); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <small class="form-text text-muted">
                                            Opsional: Pilih tahun ajar jika diperlukan.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-group mt-4">
                                <div class="d-flex justify-content-between">
                                    <button type="button" onclick="window.history.back()" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="generateBtn">
                                        <i class="fas fa-cogs"></i> Generate Tagihan Massal
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            let progressInterval;
            let isProcessing = false;

            $('#generateBulkForm').on('submit', function(e) {
                e.preventDefault();

                const kategoriStatus = $('#kategori_biaya_status').val();
                const jenisBiayaId = $('#jenis_biaya_id').val();
                const tahun = $('#tahun').val();

                console.log('Form submitted with:', {
                    kategoriStatus: kategoriStatus,
                    jenisBiayaId: jenisBiayaId,
                    tahun: tahun
                });

                if (!tahun) {
                    alert('Mohon pilih tahun.');
                    return false;
                }

                if (!kategoriStatus && !jenisBiayaId) {
                    alert('Mohon pilih minimal salah satu: Kategori Biaya atau Jenis Biaya tertentu.');
                    return false;
                }

                // Build confirm message
                let filterText = '';
                if (kategoriStatus && jenisBiayaId) {
                    filterText =
                        `Kategori: ${kategoriStatus} & Jenis: ${$('#jenis_biaya_id option:selected').text()}`;
                } else if (kategoriStatus) {
                    filterText = `Kategori: ${kategoriStatus}`;
                } else {
                    filterText = `Jenis Biaya: ${$('#jenis_biaya_id option:selected').text()}`;
                }

                // Confirm before starting
                const confirmMessage =
                    `Apakah Anda yakin ingin generate tagihan massal untuk:\n\n${filterText}\nTahun: ${tahun}\n\nProses ini mungkin memakan waktu beberapa menit.`;

                if (!confirm(confirmMessage)) {
                    return false;
                }

                // Start the process
                startBulkGenerate();
            });

            function startBulkGenerate() {
                // Disable form
                $('#generateBtn').prop('disabled', true);

                // Show progress modal
                $('#progressModal').modal('show');

                // Send request to start bulk generate
                $.ajax({
                    url: '<?php echo e(route('tagihan_terjadwal.generateBulkTerjadwal')); ?>',
                    type: 'POST',
                    data: $('#generateBulkForm').serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Start monitoring progress
                            startProgressMonitoring();
                        } else {
                            showError(response.message || 'Terjadi kesalahan saat memulai proses.');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memulai proses.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }

                        showError(errorMessage);
                    }
                });
            }

            function startProgressMonitoring() {
                progressInterval = setInterval(function() {
                    $.ajax({
                        url: '<?php echo e(route('tagihan_terjadwal.getBulkProgress')); ?>',
                        type: 'GET',
                        success: function(progress) {
                            updateProgress(progress);

                            if (progress.status === 'completed' || progress.status ===
                                'failed') {
                                clearInterval(progressInterval);
                                showResult(progress);
                            }
                        },
                        error: function() {
                            // Continue monitoring even if one request fails
                            console.error('Failed to get progress update');
                        }
                    });
                }, 1000); // Check every second
            }

            function updateProgress(progress) {
                if (progress.status === 'not_found') {
                    $('#progressText').text('Proses tidak ditemukan...');
                    return;
                }

                const current = progress.current || 0;
                const total = progress.total || 1;
                const percentage = Math.round((current / total) * 100);

                // Update progress bar
                $('.progress-bar')
                    .css('width', percentage + '%')
                    .attr('aria-valuenow', percentage)
                    .text(percentage + '%');

                // Update text
                $('#progressText').text(`Memproses ${current} dari ${total} data...`);

                if (progress.status === 'processing') {
                    $('#progressDetail').text('Sedang membuat tagihan...');
                } else if (progress.status === 'initializing') {
                    $('#progressDetail').text('Mempersiapkan data...');
                }
            }

            function showResult(progress) {
                $('#progressModal').modal('hide');

                // Force remove modal backdrop
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');

                let title, body, titleClass = 'text-success';

                if (progress.status === 'completed') {
                    title = '<i class="fas fa-check-circle text-success"></i> Generate Berhasil';
                    body = `
                    <div class="alert alert-success">
                        <h6 class="alert-heading">Proses selesai!</h6>
                        <p class="mb-2">
                            <strong>${progress.processed || 0}</strong> tagihan berhasil dibuat<br>
                            <strong>${progress.failed || 0}</strong> tagihan gagal dibuat
                        </p>
                    </div>
                `;

                    if (progress.errors && progress.errors.length > 0) {
                        body += `
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">Ada beberapa error:</h6>
                            <ul class="mb-0">
                    `;
                        progress.errors.slice(0, 5).forEach(function(error) {
                            body += `<li>${error}</li>`;
                        });
                        if (progress.errors.length > 5) {
                            body += `<li>... dan ${progress.errors.length - 5} error lainnya</li>`;
                        }
                        body += `</ul></div>`;
                    }
                } else {
                    title = '<i class="fas fa-exclamation-circle text-danger"></i> Generate Gagal';
                    titleClass = 'text-danger';
                    body = `
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">Proses gagal!</h6>
                        <p class="mb-0">Terjadi kesalahan saat generate tagihan massal.</p>
                    </div>
                `;
                }

                $('#resultModalTitle').html(title);
                $('#resultModalBody').html(body);
                $('#resultModal').modal('show');

                // Re-enable form and reset it
                $('#generateBtn').prop('disabled', false).html(
                    '<i class="fas fa-cogs"></i> Generate Tagihan Massal');

                // Reset form after successful generation
                if (progress.status === 'completed') {
                    resetForm();

                    // Auto close modal after 5 seconds for successful completion
                    setTimeout(function() {
                        if ($('#resultModal').hasClass('show')) {
                            $('#resultModal').modal('hide');
                        }
                    }, 5000);
                }
            }

            function showError(message) {
                $('#progressModal').modal('hide');

                // Force remove modal backdrop
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');

                $('#resultModalTitle').html('<i class="fas fa-exclamation-circle text-danger"></i> Error');
                $('#resultModalBody').html(`
                <div class="alert alert-danger">
                    <h6 class="alert-heading">Terjadi kesalahan!</h6>
                    <p class="mb-0">${message}</p>
                </div>
            `);
                $('#resultModal').modal('show');

                // Re-enable form
                $('#generateBtn').prop('disabled', false).html(
                    '<i class="fas fa-cogs"></i> Generate Tagihan Massal');

                // Clear interval if running
                if (progressInterval) {
                    clearInterval(progressInterval);
                }
            }

            function resetForm() {
                // Reset form fields
                $('#kategori_biaya_status').val('');
                $('#jenis_biaya_id').val('');
                $('#tahun').val('<?php echo e($currentYear); ?>');
                $('#tahun_ajar_id').val('');

                // Reset visual states
                $('.form-control').removeClass('is-valid is-invalid');
            }

            // Handle result modal close - redirect to index
            $('#resultModal').on('hidden.bs.modal', function() {
                // Force clean modal state
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');

                // Optional: Auto redirect to index after modal closes
                // Uncomment line below if you want auto redirect
                // window.location.href = '<?php echo e(route('tagihan_terjadwal.index')); ?>';
            });

            // Handle page unload/refresh warning during process
            let isProcessing = false;

            function startBulkGenerate() {
                // Set processing flag
                isProcessing = true;

                // Disable form
                $('#generateBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Memproses...');

                // Show progress modal
                $('#progressModal').modal('show');

                // Send request to start bulk generate
                $.ajax({
                    url: '<?php echo e(route('tagihan_terjadwal.generateBulkTerjadwal')); ?>',
                    type: 'POST',
                    data: $('#generateBulkForm').serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log('Generate response:', response);

                        if (response.success) {
                            // For quick response like yours, directly show result instead of monitoring
                            if (response.processed !== undefined && response.failed !== undefined) {
                                // Direct result available, show immediately
                                setTimeout(function() {
                                    showDirectResult(response);
                                }, 1000); // Small delay to show progress started
                            } else {
                                // Start monitoring progress for longer operations
                                startProgressMonitoring();
                            }
                        } else {
                            isProcessing = false;
                            showError(response.message || 'Terjadi kesalahan saat memulai proses.');
                        }
                    },
                    error: function(xhr, status, error) {
                        isProcessing = false;
                        console.error('AJAX Error:', {
                            xhr,
                            status,
                            error
                        });

                        let errorMessage = 'Terjadi kesalahan saat memulai proses.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }

                        showError(errorMessage);
                    }
                });
            }

            function showDirectResult(response) {
                $('#progressModal').modal('hide');

                // Force remove modal backdrop
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');

                const title = '<i class="fas fa-check-circle text-success"></i> Generate Berhasil';
                const body = `
                <div class="alert alert-success">
                    <h6 class="alert-heading">Proses selesai!</h6>
                    <p class="mb-2">
                        <strong>${response.processed || 0}</strong> tagihan berhasil dibuat<br>
                        <strong>${response.failed || 0}</strong> tagihan gagal dibuat
                    </p>
                    <p class="mb-0">
                        <small><i class="fas fa-info-circle"></i> ${response.message}</small>
                    </p>
                </div>
            `;

                $('#resultModalTitle').html(title);
                $('#resultModalBody').html(body);
                $('#resultModal').modal('show');

                // Re-enable form and reset it
                $('#generateBtn').prop('disabled', false).html(
                    '<i class="fas fa-cogs"></i> Generate Tagihan Massal');
                isProcessing = false;
                resetForm();
            }

            function startProgressMonitoring() {
                console.log('Starting progress monitoring...');

                progressInterval = setInterval(function() {
                    $.ajax({
                        url: '<?php echo e(route('tagihan_terjadwal.getBulkProgress')); ?>',
                        type: 'GET',
                        dataType: 'json',
                        success: function(progress) {
                            console.log('Progress update:', progress);
                            updateProgress(progress);

                            if (progress.status === 'completed' || progress.status ===
                                'failed') {
                                isProcessing = false;
                                clearInterval(progressInterval);

                                // Small delay to show final progress
                                setTimeout(function() {
                                    showResult(progress);
                                }, 500);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Progress monitoring error:', {
                                xhr,
                                status,
                                error
                            });
                            // Continue monitoring even if one request fails, but log it
                        }
                    });
                }, 1000); // Check every second
            }

            // Warn user if they try to leave during processing
            window.addEventListener('beforeunload', function(e) {
                if (isProcessing) {
                    e.preventDefault();
                    e.returnValue = 'Proses generate masih berjalan. Yakin ingin meninggalkan halaman?';
                    return e.returnValue;
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css_inline'); ?>
    <style>
        .required::after {
            content: ' *';
            color: red;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .progress {
            height: 1.5rem;
        }

        .alert-heading {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
        }

        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }

        @keyframes progress-bar-stripes {
            0% {
                background-position: 1rem 0;
            }

            100% {
                background-position: 0 0;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/tagihan-terjadwal/createBulk.blade.php ENDPATH**/ ?>