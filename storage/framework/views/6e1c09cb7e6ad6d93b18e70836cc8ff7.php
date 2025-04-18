<?php $__env->startSection('title_page', 'Tambah Permission'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Permission Baru</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('permissions.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="name">Nama Permission:</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Masukkan Nama Permission" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Simpan Permission</button>
                        <a href="<?php echo e(route('permissions.index')); ?>" class="btn btn-secondary mt-3">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/permissions/create.blade.php ENDPATH**/ ?>