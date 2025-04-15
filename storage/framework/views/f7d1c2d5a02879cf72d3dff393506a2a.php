<?php $__env->startSection('title_page', 'Bayar Pendaftaran Santri'); ?>
<?php $__env->startSection('content'); ?>

    <form action="<?php echo e(route('tagihan_terjadwal.bulkTerjadwal')); ?>" method="post">
        <?php echo csrf_field(); ?>
        <?php echo method_field('POST'); ?>
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="biaya_terjadwal_id">Biaya Terjadwal</label>
                    <select class="form-control select2 <?php $__errorArgs = ['biaya_terjadwal_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        name="biaya_terjadwal_id" required>
                        <option selected disabled>Pilih Biaya</option>
                        <?php $__currentLoopData = $biayaTerjadwals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biaya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($biaya->id_biaya_terjadwal); ?>"><?php echo e($biaya->nama_biaya); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <?php $__errorArgs = ['biaya_terjadwal_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-feedback" role="alert">
                            <strong><?php echo e($message); ?></strong>
                        </span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <button class="btn btn-primary">Buat Tagihan</button>
                    <a href="<?php echo e(route('tagihan_terjadwal.index')); ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>

    </form>

<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/tagihan-terjadwal/createBulk.blade.php ENDPATH**/ ?>