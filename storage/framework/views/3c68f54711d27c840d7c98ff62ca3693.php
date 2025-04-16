<?php $__env->startSection('title_page', 'Edit Biaya Pembayaran Pesantren'); ?>
<?php $__env->startSection('content'); ?>

    <form action="<?php echo e(route('biaya_terjadwal.store')); ?>" method="post">
        <?php echo csrf_field(); ?>
        <?php echo method_field('POST'); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nama_biaya">Nama Biaya</label>
                        <input id="nama_biaya" type="text" class="form-control <?php $__errorArgs = ['nama_biaya'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            name="nama_biaya" value="<?php echo e(old('nama_biaya')); ?>" required autocomplete="nama_biaya">

                        <?php $__errorArgs = ['nama_biaya'];
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
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input id="nominal" type="number" class="form-control <?php $__errorArgs = ['nominal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            name="nominal" value="<?php echo e(old('nominal')); ?>" required autocomplete="nominal" min=0>

                        <?php $__errorArgs = ['nominal'];
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
                        <label for="periode">Periode</label>
                        <select class="form-control <?php $__errorArgs = ['periode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="periode">
                            <option value="tahunan" <?php echo e(old('periode') == 'tahunan' ? 'selected' : ''); ?>>Tahunan</option>
                            <option value="sekali" <?php echo e(old('periode') == 'sekali' ? 'selected' : ''); ?>>Sekali</option>
                        </select>
                        <?php $__errorArgs = ['periode'];
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
                        <button class="btn btn-primary">Store</button>
                        <a href="<?php echo e(route('biaya_terjadwal.index')); ?>" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/biaya-terjadwal/create.blade.php ENDPATH**/ ?>