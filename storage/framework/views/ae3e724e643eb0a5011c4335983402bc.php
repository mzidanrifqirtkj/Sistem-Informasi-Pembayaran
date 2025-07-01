<?php $__env->startSection('title_page', 'Daftar Biaya Santri'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-12">
                <a href="<?php echo e(route('biaya-santris.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Paket Biaya
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Santri</th>
                                    <th>Jumlah Kategori</th>
                                    <th>Total Biaya</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($santri->nama_santri); ?></td>
                                        <td><?php echo e($santri->biayaSantris->count()); ?></td>
                                        <td>Rp <?php echo e(number_format($santri->total_biaya, 0, ',', '.')); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('biaya-santris.show', $santri->id_santri)); ?>"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <a href="<?php echo e(route('biaya-santris.edit', $santri->id_santri)); ?>"
                                                class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="<?php echo e(route('biaya-santris.destroy', $santri->id_santri)); ?>"
                                                method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Hapus paket biaya ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/biaya-santris/index.blade.php ENDPATH**/ ?>