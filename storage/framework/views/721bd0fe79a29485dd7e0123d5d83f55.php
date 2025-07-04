<?php $__env->startSection('title_page', 'Riwayat Tagihan Syahriah'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        /* Custom styles for better appearance */
        .stats-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stats-icon.primary {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .stats-icon.info {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .stats-icon.success {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .stats-icon.warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        /* Filter card styling */
        .filter-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .filter-card .card-body {
            padding: 1.5rem;
        }

        /* Enhanced form controls */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            padding: 0.6rem 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* Button styling */
        .btn {
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
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

        .btn-success {
            background-color: #198754;
            border: none;
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
        }

        .btn-info {
            background-color: #0dcaf0;
            border: none;
            box-shadow: 0 4px 12px rgba(13, 202, 240, 0.3);
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        /* Table styling */
        .table-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
            white-space: nowrap;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Monthly status grid */
        .monthly-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 8px;
            padding: 5px;
        }

        .month-status {
            text-align: center;
            padding: 5px;
            border-radius: 8px;
            background-color: #f8f9fa;
            position: relative;
        }

        .month-status:hover {
            background-color: #e9ecef;
        }

        .status-icon {
            font-size: 1.1rem;
            display: block;
            margin-bottom: 2px;
        }

        .status-icon.lunas {
            color: #198754;
        }

        .status-icon.sebagian {
            color: #ffc107;
        }

        .status-icon.belum {
            color: #dc3545;
        }

        .status-icon.empty {
            color: #dee2e6;
        }

        .month-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: #6c757d;
        }

        /* Pagination styling */
        .pagination {
            margin: 0;
        }

        .page-link {
            border-radius: 8px;
            margin: 0 3px;
            border: none;
            color: #495057;
            font-weight: 500;
            padding: 0.5rem 1rem;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background-color: #e9ecef;
            color: #0d6efd;
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            color: white;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .page-item.disabled .page-link {
            background-color: #e9ecef;
            color: #adb5bd;
        }

        /* Filter row improvements */
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: end;
        }

        .filter-row .form-group {
            flex: 1 1 200px;
            min-width: 180px;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 1.5rem;
            justify-content: flex-end;
        }

        /* Sortable column indicator */
        .sortable {
            cursor: pointer;
            position: relative;
            user-select: none;
        }

        .sortable:hover {
            background-color: #e9ecef;
        }

        .sortable::after {
            content: '\f0dc';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 10px;
            color: #adb5bd;
            font-size: 0.8rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .monthly-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 5px;
            }

            .stats-card {
                margin-bottom: 15px;
            }

            .btn {
                padding: 0.4rem 1rem;
                font-size: 0.875rem;
            }

            .table {
                font-size: 0.875rem;
            }

            .hide-mobile {
                display: none !important;
            }

            .filter-actions {
                justify-content: center;
                width: 100%;
            }

            .month-status {
                padding: 3px;
            }

            .status-icon {
                font-size: 0.9rem;
            }

            .month-label {
                font-size: 0.6rem;
            }
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-content {
            text-align: center;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #0d6efd;
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

        /* Tooltip styling */
        .tooltip-inner {
            background-color: #495057;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
        }
    </style>

    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0 text-dark fw-bold">Tagihan Bulanan</h2>
                <?php if(!auth()->user()->hasRole('santri')): ?>
                    <p class="text-muted mb-0">Kelola tagihan bulanan santri</p>
                <?php endif; ?>
            </div>
            <?php if(!auth()->user()->hasRole('santri')): ?>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <a href="<?php echo e(route('tagihan_bulanan.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Buat Individual
                        </a>
                        <a href="<?php echo e(route('tagihan_bulanan.createBulkBulanan')); ?>" class="btn btn-success">
                            <i class="fas fa-file-invoice me-1"></i> Generate Massal
                        </a>
                        <button type="button" class="btn btn-info text-white" onclick="exportData()">
                            <i class="fas fa-file-excel me-1"></i> Export
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Statistics Cards -->
        <?php if(!auth()->user()->hasRole('santri')): ?>
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Total Tagihan</h6>
                                    <h3 class="mb-0 fw-bold"><?php echo e(number_format($stats['total_tagihan'])); ?></h3>
                                    <small class="text-muted">Tahun <?php echo e($tahun); ?></small>
                                </div>
                                <div class="stats-icon primary">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Total Nominal</h6>
                                    <h4 class="mb-0 fw-bold"><?php echo e(number_format($stats['total_nominal'], 0, ',', '.')); ?></h4>
                                    <small class="text-muted">Rupiah</small>
                                </div>
                                <div class="stats-icon info">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Total Dibayar</h6>
                                    <h4 class="mb-0 fw-bold"><?php echo e(number_format($stats['total_dibayar'], 0, ',', '.')); ?></h4>
                                    <small class="text-muted">Rupiah</small>
                                </div>
                                <div class="stats-icon success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Collection Rate</h6>
                                    <h3 class="mb-0 fw-bold"><?php echo e($stats['collection_rate']); ?>%</h3>
                                    <small class="text-muted">Persentase</small>
                                </div>
                                <div class="stats-icon warning">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Filter Section -->
        <?php if(!auth()->user()->hasRole('santri')): ?>
            <div class="card filter-card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-filter me-2"></i>Filter Data
                    </h5>
                    <form method="GET" action="<?php echo e(route('tagihan_bulanan.index')); ?>" id="filterForm">
                        <div class="filter-row">
                            <div class="form-group">
                                <label class="form-label">Nama Santri / NIS</label>
                                <input type="text" name="nama_santri" class="form-control"
                                    placeholder="Cari nama atau NIS..." value="<?php echo e(request('nama_santri')); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kelas</label>
                                <select name="kelas_id" class="form-select">
                                    <option value="">Semua Kelas</option>
                                    <option value="tanpa_kelas"
                                        <?php echo e(request('kelas_id') == 'tanpa_kelas' ? 'selected' : ''); ?>>
                                        Tanpa Kelas
                                    </option>
                                    <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($kelas->id_kelas); ?>"
                                            <?php echo e(request('kelas_id') == $kelas->id_kelas ? 'selected' : ''); ?>>
                                            <?php echo e($kelas->nama_kelas); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tahun</label>
                                <select name="tahun" class="form-select">
                                    <?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($year); ?>" <?php echo e($tahun == $year ? 'selected' : ''); ?>>
                                            <?php echo e($year); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="lunas" <?php echo e(request('status') == 'lunas' ? 'selected' : ''); ?>>Lunas
                                    </option>
                                    <option value="dibayar_sebagian"
                                        <?php echo e(request('status') == 'dibayar_sebagian' ? 'selected' : ''); ?>>Dibayar Sebagian
                                    </option>
                                    <option value="belum_lunas" <?php echo e(request('status') == 'belum_lunas' ? 'selected' : ''); ?>>
                                        Belum Lunas</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Bulan</label>
                                <select name="bulan" class="form-select">
                                    <option value="">Semua Bulan</option>
                                    <?php $__currentLoopData = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bulan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($bulan); ?>"
                                            <?php echo e(request('bulan') == $bulan ? 'selected' : ''); ?>>
                                            <?php echo e($bulan); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="filter-actions">
                            <a href="<?php echo e(route('tagihan_bulanan.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Data Table -->
        <div class="card table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tagihanTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Santri</th>
                                <?php if(!auth()->user()->hasRole('santri')): ?>
                                    <th class="hide-mobile sortable" onclick="sortTable('nis')">
                                        NIS <i class="fas fa-sort ms-1"></i>
                                    </th>
                                    <th class="hide-mobile">Kelas</th>
                                <?php endif; ?>
                                <th class="text-center">Status Bulanan <?php echo e($tahun); ?></th>
                                <th>Total Tagihan</th>
                                <th class="hide-mobile">Total Dibayar</th>
                                <th>Kekurangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($santris->firstItem() + $index); ?></td>
                                    <td class="fw-semibold"><?php echo e($santri->nama_santri); ?></td>
                                    <?php if(!auth()->user()->hasRole('santri')): ?>
                                        <td class="hide-mobile"><?php echo e($santri->nis); ?></td>
                                        <td class="hide-mobile">
                                            <span class="badge bg-secondary"><?php echo e($santri->nama_kelas_aktif); ?></span>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <div class="monthly-grid">
                                            <?php $__currentLoopData = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bulan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $status = $santri->monthly_status[$bulan] ?? null;
                                                    $iconClass = match ($status) {
                                                        'lunas' => 'fas fa-check-circle status-icon lunas',
                                                        'dibayar_sebagian'
                                                            => 'fas fa-exclamation-circle status-icon sebagian',
                                                        'belum_lunas' => 'fas fa-times-circle status-icon belum',
                                                        default => 'far fa-circle status-icon empty',
                                                    };
                                                    $title = match ($status) {
                                                        'lunas' => 'Lunas',
                                                        'dibayar_sebagian' => 'Dibayar Sebagian',
                                                        'belum_lunas' => 'Belum Lunas',
                                                        default => 'Belum Ada Tagihan',
                                                    };
                                                ?>
                                                <div class="month-status" data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="<?php echo e($bulan); ?>: <?php echo e($title); ?>">
                                                    <i class="<?php echo e($iconClass); ?>"></i>
                                                    <div class="month-label"><?php echo e(substr($bulan, 0, 1)); ?></div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">Rp
                                            <?php echo e(number_format($santri->total_nominal, 0, ',', '.')); ?></span>
                                    </td>
                                    <td class="hide-mobile text-success">
                                        Rp <?php echo e(number_format($santri->total_dibayar, 0, ',', '.')); ?>

                                    </td>
                                    <td>
                                        <?php if($santri->total_kekurangan > 0): ?>
                                            <span class="text-danger fw-bold">
                                                Rp <?php echo e(number_format($santri->total_kekurangan, 0, ',', '.')); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-success">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('tagihan_bulanan.show', ['id' => $santri->id_santri, 'tahun' => $tahun])); ?>"
                                            class="btn btn-info btn-sm text-white" data-bs-toggle="tooltip"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p class="mb-0">Tidak ada data tagihan bulanan</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($santris->hasPages()): ?>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Menampilkan <?php echo e($santris->firstItem()); ?> - <?php echo e($santris->lastItem()); ?> dari
                                <?php echo e($santris->total()); ?> data
                            </div>
                            <div>
                                <?php echo e($santris->appends(request()->query())->links('pagination::bootstrap-4')); ?>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display:none;">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p class="mt-3 text-muted">Memuat data...</p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-save form data to localStorage
            const formData = localStorage.getItem('tagihan_bulanan_filter');
            if (formData && !window.location.search) {
                const data = JSON.parse(formData);
                Object.keys(data).forEach(key => {
                    $(`[name="${key}"]`).val(data[key]);
                });
            }

            // Save form data on change
            $('#filterForm input, #filterForm select').on('change', function() {
                const data = {};
                $('#filterForm').serializeArray().forEach(item => {
                    if (item.value) data[item.name] = item.value;
                });
                localStorage.setItem('tagihan_bulanan_filter', JSON.stringify(data));
            });

            // Show loading on form submit
            $('#filterForm').on('submit', function() {
                $('#loadingOverlay').fadeIn();
            });
        });

        function exportData() {
            $('#loadingOverlay').fadeIn();
            const params = new URLSearchParams($('#filterForm').serialize());
            window.location.href = "<?php echo e(route('tagihan_bulanan.export')); ?>?" + params.toString();
            setTimeout(() => {
                $('#loadingOverlay').fadeOut();
            }, 2000);
        }

        // Simple sort function
        let sortOrder = 'asc';

        function sortTable(column) {
            $('#loadingOverlay').fadeIn();

            // Add sort parameter to current URL
            const url = new URL(window.location.href);
            url.searchParams.set('sort', column);
            url.searchParams.set('order', sortOrder === 'asc' ? 'desc' : 'asc');

            window.location.href = url.toString();
        }

        // Prevent multiple form submissions
        let isSubmitting = false;
        $('#filterForm').on('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
            $(this).find('button[type="submit"]').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin me-1"></i> Memfilter...');
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/tagihan_bulanan/index.blade.php ENDPATH**/ ?>