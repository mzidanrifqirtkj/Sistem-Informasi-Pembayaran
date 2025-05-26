<?php $__env->startSection('title_page', 'Edit Item Tambahan Bulanan'); ?>
<?php $__env->startSection('content'); ?>

    <form action="<?php echo e(route('tambahan_bulanan.item_santri.update', $santri)); ?>" method="post">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="container">
            <div class="row mb-3">
                <div class="col">
                    <h4>Edit Tambahan Bulanan untuk: <?php echo e($santri->nama_santri); ?></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama Item</th>
                                <th scope="col">Aktif</th>
                                <th scope="col">Jumlah (Nominal)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    // Cek apakah item sudah dimiliki santri
                                    $pivot = $santri->tambahanBulanans
                                        ->where('id_tambahan_bulanan', $item->id_tambahan_bulanan)
                                        ->first();
                                ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($item->nama_item); ?></td>
                                    <td>
                                        <input type="checkbox" name="items[<?php echo e($item->id_tambahan_bulanan); ?>][aktif]"
                                            value="1" <?php echo e($pivot ? 'checked' : ''); ?>>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control"
                                            name="items[<?php echo e($item->id_tambahan_bulanan); ?>][jumlah]"
                                            value="<?php echo e(old('items.' . $item->id_tambahan_bulanan . '.jumlah', $pivot->pivot->jumlah ?? 0)); ?>"
                                            min="0" <?php echo e(!$pivot ? 'disabled' : ''); ?>>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?php echo e(route('tambahan_bulanan.index')); ?>" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        // Mengaktifkan/menonaktifkan input jumlah berdasarkan checkbox
        document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const jumlahInput = this.closest('tr').querySelector('input[type="number"]');
                if (this.checked) {
                    jumlahInput.removeAttribute('disabled');
                } else {
                    jumlahInput.setAttribute('disabled', 'disabled');
                    jumlahInput.value = 0; // Reset nilai jika dinonaktifkan
                }
            });
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/tambahan-bulanan/item-santri-edit.blade.php ENDPATH**/ ?>