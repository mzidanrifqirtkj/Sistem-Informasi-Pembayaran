<?php $__env->startSection('title_page', 'Biaya Terjadwal'); ?>
<?php $__env->startSection('content'); ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?php echo e(route('biaya_terjadwal.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Tambah Biaya
        </a>
    </div>

    <div class="row">
        <!-- Dana Tahunan Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Dana Tahunan</h4>
                    <span class="badge bg-light text-primary"><?php echo e($biayaTerjadwals->where('periode', 'tahunan')->count()); ?>

                        Item</span>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $biayaTerjadwals->filter(function($item) { return strtolower($item->periode) === 'tahunan'; }); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biaya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 font-weight-bold"><?php echo e($biaya->nama_biaya); ?></h5>

                            </div>
                            <div class="text-right">
                                <h4 class="text-success mb-1">Rp <?php echo e(number_format($biaya->nominal, 0, ',', '.')); ?></h4>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('biaya_terjadwal.edit', $biaya->id_biaya_terjadwal)); ?>"
                                        class="btn btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger"
                                        onclick="deleteBiayaTerjadwal('<?php echo e($biaya->id_biaya_terjadwal); ?>')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>Tidak ada data dana tahunan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Dana Eksidental Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-exclamation-circle mr-2"></i>Dana Eksidental</h4>
                    <span class="badge bg-light text-warning"><?php echo e($biayaTerjadwals->where('periode', 'sekali')->count()); ?>

                        Item</span>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $biayaTerjadwals->filter(function($item) { return strtolower($item->periode) === 'sekali'; }); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biaya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 font-weight-bold"><?php echo e($biaya->nama_biaya); ?></h5>

                            </div>
                            <div class="text-right">
                                <h4 class="text-success mb-1">Rp <?php echo e(number_format($biaya->nominal, 0, ',', '.')); ?></h4>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('biaya_terjadwal.edit', $biaya->id_biaya_terjadwal)); ?>"
                                        class="btn btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger"
                                        onclick="deleteBiayaTerjadwal('<?php echo e($biaya->id_biaya_terjadwal); ?>')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>Tidak ada data dana eksidental</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteBiayaTerjadwalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteFormTerjadwal" method="post">
                <?php echo method_field('DELETE'); ?>
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus biaya ini? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" onclick="formSubmitTerjadwal()" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        function deleteBiayaTerjadwal(id) {
            let url = '<?php echo e(route('biaya_terjadwal.destroy', ':id')); ?>';
            url = url.replace(':id', id);
            $("#deleteFormTerjadwal").attr('action', url);
            $("#deleteBiayaTerjadwalModal").modal('show');
        }

        function formSubmitTerjadwal() {
            $("#deleteFormTerjadwal").submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/biaya-terjadwal/index.blade.php ENDPATH**/ ?>