<?php $__env->startSection('title_page', 'Daftar Permission'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="<?php echo e(route('permissions.create')); ?>" class="btn btn-primary">Tambah Permission</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="permissionsTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Permission</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($key + 1); ?></td>
                        <td><?php echo e($permission->name); ?></td>
                        <td>
                            <a href="<?php echo e(route('permissions.edit', $permission)); ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form action="<?php echo e(route('permissions.destroy', $permission)); ?>" method="POST"
                                style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/permissions/index.blade.php ENDPATH**/ ?>