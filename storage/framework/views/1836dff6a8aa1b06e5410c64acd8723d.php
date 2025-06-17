<?php $__env->startSection('title_page', 'Data Tahun Ajar'); ?>
<?php $__env->startSection('content'); ?>

    <form action="<?php echo e(route('tahun_ajar.update', $tahunAjar)); ?>" method="post">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="tahun_ajar"><?php echo e(__('Periode Tahun Ajar')); ?></label>
                        <input id="tahun_ajar" type="tahun_ajar" class="form-control <?php $__errorArgs = ['tahun_ajar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            name="tahun_ajar" value="<?php echo e(old('tahun_ajar', $tahunAjar->tahun_ajar)); ?>" required
                            autocomplete="tahun_ajar" autofocus>

                        <?php $__errorArgs = ['tahun_ajar'];
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
                        <button class="btn btn-primary">Edit</button>
                        <a href="<?php echo e(route('tahun_ajar.index')); ?>" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/tahun-ajar/edit.blade.php ENDPATH**/ ?>