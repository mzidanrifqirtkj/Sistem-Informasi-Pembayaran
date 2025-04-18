<?php $__env->startSection('title_page', 'Edit Role'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-8">
            <form action="<?php echo e(route('roles.update', $role)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="form-group">
                    <label>Role Name</label>
                    <input type="text" name="name" value="<?php echo e($role->name); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Permissions</label><br>
                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="<?php echo e($permission->name); ?>"
                                <?php echo e(in_array($permission->name, $rolePermissions) ? 'checked' : ''); ?>

                                class="form-check-input">
                            <label class="form-check-label"><?php echo e($permission->name); ?></label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/roles/edit.blade.php ENDPATH**/ ?>