<?php $__env->startSection('title_page', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(session('warning')): ?>
        <div class="alert alert-warning">
            <?php echo e(session('warning')); ?>

        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <h5>Selamat datang di Dashboard</h5>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/home.blade.php ENDPATH**/ ?>