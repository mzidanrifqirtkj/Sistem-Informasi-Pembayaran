<?php $__env->startSection('title_page', 'Biaya Pembayaran'); ?>
<?php $__env->startSection('content'); ?>


    <h2 class="text-center my-4">Biaya Terjadwal</h2>
    <div class="row">
        <div class="col-md-2 mb-3">
            
            <a href="<?php echo e(route('biaya_terjadwal.create')); ?>" class="btn btn-primary">Tambah Biaya</a><br><br>
            
        </div>
    </div>
    <div class="row g-4">
        <?php $__currentLoopData = $biayaTerjadwals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biaya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white text-center">
                        <h5 class="mb-0"><?php echo e($biaya->nama_biaya); ?></h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-success mb-2">Rp. <?php echo e(number_format($biaya->nominal, 2, ',', '.')); ?></h3>
                        <p class="text-muted">(<?php echo e($biaya->periode); ?>)</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?php echo e(route('biaya_terjadwal.edit', $biaya->id_biaya_terjadwal)); ?>"
                                class="btn btn-sm btn-info">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-danger"
                                onclick="deleteBiayaTerjadwal('<?php echo e($biaya->id_biaya_terjadwal); ?>')" data-toggle="modal"
                                data-target="#deleteSantriModal">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('modal'); ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteBiayaTerjadwalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteFormBulanan" method="post">
                <?php echo method_field('DELETE'); ?>
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter">Hapus Biaya Terjadwal</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" onclick="formSubmitBulanan()" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('script'); ?>
    <script>
        // Fungsi untuk biaya_terjadwal
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