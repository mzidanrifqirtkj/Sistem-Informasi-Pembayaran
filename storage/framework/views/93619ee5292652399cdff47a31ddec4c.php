<?php $__env->startSection('title_page', 'Edit Paket Biaya Santri'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Paket Biaya Santri: <?php echo e($santri->nama_santri); ?></h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('biaya-santris.update', $santri->id_santri)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <div class="form-group">
                                <label for="santri_id">Santri</label>
                                <?php if(auth()->user()->hasRole('santri')): ?>
                                    <input type="hidden" name="santri_id" value="<?php echo e($santri->id_santri); ?>">
                                    <input type="text" class="form-control" value="<?php echo e($santri->nama_santri); ?>" readonly>
                                <?php else: ?>
                                    <select class="form-control" name="santri_id" id="santri_id" required>
                                        <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id_santri); ?>"
                                                <?php echo e($item->id_santri == $santri->id_santri ? 'selected' : ''); ?>>
                                                <?php echo e($item->nama_santri); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label>Biaya</label>
                                <div id="biaya-container">
                                    <?php $__currentLoopData = $santri->biayaSantris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biaya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="biaya-item mb-3 p-3 border rounded">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <select class="form-control biaya-select"
                                                        name="biaya[<?php echo e($loop->index); ?>][id]" required>
                                                        <?php $__currentLoopData = $daftarBiayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($item->id_daftar_biaya); ?>"
                                                                data-nominal="<?php echo e($item->nominal); ?>"
                                                                <?php echo e($item->id_daftar_biaya == $biaya->daftar_biaya_id ? 'selected' : ''); ?>>
                                                                <?php echo e($item->kategoriBiaya->nama_kategori); ?> - Rp
                                                                <?php echo e(number_format($item->nominal, 0, ',', '.')); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" name="biaya[<?php echo e($loop->index); ?>][jumlah]"
                                                        class="form-control jumlah" value="<?php echo e($biaya->jumlah); ?>"
                                                        min="1" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" class="btn btn-danger btn-sm remove-biaya">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <button type="button" id="tambah-biaya" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Tambah Biaya
                                </button>
                            </div>

                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="<?php echo e(route('biaya-santris.show', $santri->id_santri)); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            $('#tambah-biaya').click(function() {
                const index = Date.now();
                const newItem = `
                <div class="biaya-item mb-3 p-3 border rounded">
                    <div class="row">
                        <div class="col-md-5">
                            <select class="form-control biaya-select" name="biaya[${index}][id]" required>
                                <option value="">-- Pilih Biaya --</option>
                                <?php $__currentLoopData = $daftarBiayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id_daftar_biaya); ?>" data-nominal="<?php echo e($item->nominal); ?>">
                                        <?php echo e($item->kategoriBiaya->nama_kategori); ?> - Rp <?php echo e(number_format($item->nominal, 0, ',', '.')); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="biaya[${index}][jumlah]"
                                class="form-control jumlah" value="1" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-danger btn-sm remove-biaya">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
                `;
                $('#biaya-container').append(newItem);
            });

            $(document).on('click', '.remove-biaya', function() {
                $(this).closest('.biaya-item').remove();
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/biaya-santris/edit.blade.php ENDPATH**/ ?>