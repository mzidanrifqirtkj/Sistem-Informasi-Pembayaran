<?php $__env->startSection('title_page', 'Detail Tagihan - ' . $santri->nama_santri); ?>

<?php $__env->startSection('content'); ?>
    <style>
        /* Enhanced styling */
        .month-card {
            border-radius: 12px;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .month-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .month-card.has-tagihan {
            border-left: 4px solid #28a745;
        }

        .month-card.no-tagihan {
            border-left: 4px solid #dc3545;
            opacity: 0.7;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-lunas {
            background-color: #28a745;
        }

        .status-sebagian {
            background-color: #ffc107;
        }

        .status-belum {
            background-color: #dc3545;
        }

        .status-none {
            background-color: #6c757d;
        }
    </style>

    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="mb-0">Detail Tagihan - <?php echo e($santri->nama_santri); ?></h2>
                <?php if(!auth()->user()->hasRole('santri')): ?>
                    <p class="text-muted mb-0">NIS: <?php echo e($santri->nis); ?> | Kelas: <?php echo e($santri->nama_kelas_aktif); ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <select class="form-select" id="tahunSelect" onchange="changeTahun()">
                        <?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($year); ?>" <?php echo e($year == $tahun ? 'selected' : ''); ?>>
                                Tahun <?php echo e($year); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <a href="<?php echo e(route('tagihan_bulanan.index')); ?>" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <?php
                $totalTagihan = $santri->tagihanBulanan->count();
                $totalLunas = $santri->tagihanBulanan->where('status', 'lunas')->count();
                $totalNominal = $santri->tagihanBulanan->sum('nominal');
                $totalDibayar = $santri->tagihanBulanan->sum('total_pembayaran');
                $sisaTagihan = $totalNominal - $totalDibayar;
            ?>

            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h4><?php echo e($totalTagihan); ?></h4>
                        <p class="mb-0">Total Tagihan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h4><?php echo e($totalLunas); ?></h4>
                        <p class="mb-0">Sudah Lunas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h4>Rp <?php echo e(number_format($totalDibayar, 0, ',', '.')); ?></h4>
                        <p class="mb-0">Total Dibayar</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h4>Rp <?php echo e(number_format($sisaTagihan, 0, ',', '.')); ?></h4>
                        <p class="mb-0">Sisa Tagihan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Tagihan -->
        <div class="row">
            <?php $__currentLoopData = $allMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthKey => $monthName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $tagihan = $tagihansByMonth->get($monthKey);
                    $hasTagihan = $tagihan !== null;
                ?>

                <div class="col-lg-4 col-md-6">
                    <div class="card month-card <?php echo e($hasTagihan ? 'has-tagihan' : 'no-tagihan'); ?>">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <?php if($hasTagihan): ?>
                                        <span
                                            class="status-indicator status-<?php echo e($tagihan->status === 'lunas' ? 'lunas' : ($tagihan->status === 'dibayar_sebagian' ? 'sebagian' : 'belum')); ?>"></span>
                                    <?php else: ?>
                                        <span class="status-indicator status-none"></span>
                                    <?php endif; ?>
                                    <?php echo e($monthName); ?>

                                </h5>
                                <?php if($hasTagihan): ?>
                                    <span class="badge bg-<?php echo e($tagihan->status_color); ?>">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $tagihan->status))); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Belum Ada</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-body">
                            <?php if($hasTagihan): ?>
                                <!-- Tagihan Info -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Nominal:</span>
                                        <strong>Rp <?php echo e(number_format($tagihan->nominal, 0, ',', '.')); ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Dibayar:</span>
                                        <strong class="text-success">Rp
                                            <?php echo e(number_format($tagihan->total_pembayaran, 0, ',', '.')); ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Sisa:</span>
                                        <strong class="<?php echo e($tagihan->sisa_tagihan > 0 ? 'text-danger' : 'text-success'); ?>">
                                            Rp <?php echo e(number_format($tagihan->sisa_tagihan, 0, ',', '.')); ?>

                                        </strong>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="btn-group w-100">
                                    <?php if($tagihan->canEdit()): ?>
                                        <a href="<?php echo e(route('tagihan_bulanan.edit', $tagihan->id_tagihan_bulanan)); ?>"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if($tagihan->canDelete()): ?>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="deleteTagihan(<?php echo e($tagihan->id_tagihan_bulanan); ?>, '<?php echo e($monthName); ?>')"
                                            data-bs-toggle="tooltip" title="Hapus Tagihan <?php echo e($monthName); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary btn-sm" disabled
                                            data-bs-toggle="tooltip" title="Tidak bisa dihapus - sudah ada pembayaran">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Add Payment Button -->
                                    <?php if($tagihan->status !== 'lunas'): ?>
                                        <button type="button" class="btn btn-success btn-sm"
                                            onclick="addPayment(<?php echo e($tagihan->id_tagihan_bulanan); ?>, '<?php echo e($monthName); ?>')"
                                            data-bs-toggle="tooltip" title="Tambah Pembayaran">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Payment History -->
                                <?php if($tagihan->pembayarans->count() > 0 || $tagihan->paymentAllocations->count() > 0): ?>
                                    <hr>
                                    <small class="text-muted">Riwayat Pembayaran:</small>
                                    <?php $__currentLoopData = $tagihan->pembayarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pembayaran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="small">
                                            <i class="fas fa-money-bill text-success"></i>
                                            <?php echo e($pembayaran->tanggal_pembayaran->format('d/m/Y')); ?> -
                                            Rp <?php echo e(number_format($pembayaran->nominal_pembayaran, 0, ',', '.')); ?>

                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $tagihan->paymentAllocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allocation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="small">
                                            <i class="fas fa-share text-info"></i>
                                            <?php echo e($allocation->pembayaran->tanggal_pembayaran->format('d/m/Y')); ?> -
                                            Rp <?php echo e(number_format($allocation->allocated_amount, 0, ',', '.')); ?> (Alokasi)
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- No Tagihan -->
                                <p class="text-muted mb-3">Belum ada tagihan untuk bulan ini</p>
                                <a href="<?php echo e(route('tagihan_bulanan.create', ['santri_id' => $santri->id_santri, 'bulan' => $monthKey, 'tahun' => $tahun])); ?>"
                                    class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-plus me-2"></i>Buat Tagihan
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function changeTahun() {
            const tahun = document.getElementById('tahunSelect').value;
            window.location.href = `<?php echo e(route('tagihan_bulanan.show', $santri->id_santri)); ?>/${tahun}`;
        }

        function deleteTagihan(tagihanId, monthName) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Hapus tagihan <strong><?php echo e($santri->nama_santri); ?></strong> untuk bulan <strong>${monthName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/tagihan-bulanan/${tagihanId}`;

                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '_token';
                    csrfField.value = '<?php echo e(csrf_token()); ?>';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfField);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function addPayment(tagihanId, monthName) {
            // Redirect to payment creation or open modal
            window.location.href = `/pembayaran/create?tagihan_id=${tagihanId}`;
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/tagihan_bulanan/show.blade.php ENDPATH**/ ?>