<?php $__env->startSection('title_page', 'Detail Paket Biaya Santri'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Paket Biaya Santri: <?php echo e($santri->nama_santri); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Nama Santri: <?php echo e($santri->nama_santri); ?></h5>
                            </div>
                            <div class="col-md-6 text-right">
                                <h5>Total Biaya: Rp <?php echo e(number_format($totalBiaya, 0, ',', '.')); ?></h5>
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kategori Biaya</th>
                                    <th>Nominal</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $santri->biayaSantris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biaya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($biaya->daftarBiaya->kategoriBiaya->nama_kategori); ?></td>
                                        <td>Rp <?php echo e(number_format($biaya->daftarBiaya->nominal, 0, ',', '.')); ?></td>
                                        <td><?php echo e($biaya->jumlah); ?></td>
                                        <td>Rp
                                            <?php echo e(number_format($biaya->daftarBiaya->nominal * $biaya->jumlah, 0, ',', '.')); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>

                        <div class="text-right mt-3">
                            <a href="<?php echo e(route('biaya-santris.edit', $santri->id_santri)); ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Paket
                            </a>
                            <form action="<?php echo e(route('biaya-santris.destroy', $santri->id_santri)); ?>" method="POST"
                                class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Hapus paket biaya ini?')">
                                    <i class="fas fa-trash"></i> Hapus Paket
                                </button>
                            </form>
                            <a href="<?php echo e(route('biaya-santris.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/biaya-santris/show.blade.php ENDPATH**/ ?>