<?php $__env->startSection('title_page', 'Pembayaran Santri'); ?>
<?php $__env->startSection('content'); ?>


    <div class="container">
        <h1>Pilih Santri untuk Membayar Tagihan</h1>
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($s->nis); ?></td>
                        <td><?php echo e($s->nama_santri); ?></td>
                        <td><?php echo e($s->kategoriSantri->nama_kategori); ?></td>
                        <td>
                            <a href="<?php echo e(route('pembayaran.show', $s->id_santri)); ?>" class="btn btn-primary">Lihat Tagihan</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/pembayaran/index.blade.php ENDPATH**/ ?>