<?php $__env->startSection('title_page', 'Detail Paket Biaya Santri'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Paket Biaya Santri: <?php echo e($santri->nama_santri); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Nama Santri: <?php echo e($santri->nama_santri); ?></h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5>Total Biaya: Rp <?php echo e(number_format($totalBiaya, 0, ',', '.')); ?></h5>
                            </div>
                        </div>

                        <!-- Summary Count per Status -->
                        <div class="row mb-4">
                            <?php
                                $statusSummary = [];
                                $statusColors = [
                                    'tahunan' => 'primary',
                                    'insidental' => 'success',
                                    'tambahan' => 'warning',
                                    'jalur' => 'info',
                                ];

                                foreach ($santri->biayaSantris as $biaya) {
                                    $status = $biaya->daftarBiaya->kategoriBiaya->status;
                                    if (!isset($statusSummary[$status])) {
                                        $statusSummary[$status] = 0;
                                    }
                                    $statusSummary[$status]++;
                                }
                            ?>

                            <?php $__currentLoopData = $statusSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <div class="alert alert-<?php echo e($statusColors[$status]); ?> mb-0 text-center">
                                        <strong class="text-uppercase"><?php echo e($status); ?></strong><br>
                                        <span class="badge bg-white text-<?php echo e($statusColors[$status]); ?>"><?php echo e($count); ?>

                                            item<?php echo e($count > 1 ? 's' : ''); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <!-- Tables grouped by Status -->
                        <?php $__currentLoopData = ['tahunan', 'insidental', 'tambahan', 'jalur']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currentStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $biayaByStatus = $santri->biayaSantris->filter(function ($biaya) use ($currentStatus) {
                                    return $biaya->daftarBiaya->kategoriBiaya->status === $currentStatus;
                                });

                                if ($biayaByStatus->isEmpty()) {
                                    continue;
                                }

                                $statusColor = $statusColors[$currentStatus];
                            ?>

                            <div class="mb-4">
                                <!-- Status Header -->
                                <div class="card border">
                                    <div class="card-header bg-<?php echo e($statusColor); ?> text-white">
                                        <h5 class="mb-0">
                                            <i
                                                class="fas fa-<?php echo e($currentStatus == 'tahunan' ? 'calendar-alt' : ($currentStatus == 'insidental' ? 'hand-holding-usd' : ($currentStatus == 'tambahan' ? 'plus-circle' : 'route'))); ?> me-2"></i>
                                            Biaya <?php echo e(ucfirst($currentStatus)); ?>

                                            <span
                                                class="badge bg-white text-<?php echo e($statusColor); ?> ms-2"><?php echo e($biayaByStatus->count()); ?>

                                                items</span>
                                        </h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="35%">Kategori Biaya</th>
                                                    <th width="22%">Nominal</th>
                                                    <th width="15%">Jumlah</th>
                                                    <th width="20%">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $biayaByStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $biaya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr class="border">
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span
                                                                    class="fw-semibold"><?php echo e($biaya->daftarBiaya->kategoriBiaya->nama_kategori); ?></span>
                                                                <span class="badge bg-<?php echo e($statusColor); ?> text-white mt-1"
                                                                    style="width: fit-content; font-size: 0.7rem;">
                                                                    <?php echo e(strtoupper($currentStatus)); ?>

                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td>Rp
                                                            <?php echo e(number_format($biaya->daftarBiaya->nominal, 0, ',', '.')); ?>

                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-secondary"><?php echo e($biaya->jumlah); ?>x</span>
                                                        </td>
                                                        <td class="fw-bold text-<?php echo e($statusColor); ?>">
                                                            Rp
                                                            <?php echo e(number_format($biaya->daftarBiaya->nominal * $biaya->jumlah, 0, ',', '.')); ?>

                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <div class="text-end mt-3">
                            <a href="<?php echo e(route('biaya-santris.edit', $santri->id_santri)); ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Paket
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Hapus Paket
                            </button>
                            <a href="<?php echo e(route('biaya-santris.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                        <h6>Apakah Anda yakin ingin menghapus paket biaya untuk:</h6>
                        <p class="fw-bold text-primary fs-5"><?php echo e($santri->nama_santri); ?></p>
                        <p class="text-muted">Tindakan ini tidak dapat dibatalkan!</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <form action="<?php echo e(route('biaya-santris.destroy', $santri->id_santri)); ?>" method="POST"
                        class="d-inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Ya, Hapus!
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css_inline'); ?>
    <style>
        .border-start {
            border-left-width: 4px !important;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        .badge {
            font-size: 0.7rem;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/biaya-santris/show.blade.php ENDPATH**/ ?>