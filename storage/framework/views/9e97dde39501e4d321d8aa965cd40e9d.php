<?php $__env->startSection('title_page', 'Edit Riwayat Kelas'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <h4>Edit Riwayat Kelas</h4>

        <form action="<?php echo e(route('riwayat-kelas.update', $riwayat->id_riwayat_kelas)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="form-group mb-3">
                <label for="santri_id">Santri</label>
                <select name="santri_id" id="santri_id" class="form-control select2">
                    <option value="">-- Pilih Santri --</option>
                    <?php $__currentLoopData = $santri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id_santri); ?>"
                            <?php echo e($riwayat->santri_id == $item->id_santri ? 'selected' : ''); ?>>
                            <?php echo e($item->nama_santri); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['santri_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <small class="text-danger"><?php echo e($message); ?></small>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group mb-3">
                <label for="mapel_kelas_id">Mapel - Kelas - Tahun Ajar</label>
                <select name="mapel_kelas_id" id="mapel_kelas_id" class="form-control select2">
                    <option value="">-- Pilih Mapel Kelas --</option>
                    <?php $__currentLoopData = $mapelKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id_mapel_kelas); ?>"
                            <?php echo e($riwayat->mapel_kelas_id == $item->id_mapel_kelas ? 'selected' : ''); ?>>
                            <?php echo e($item->mataPelajaran->nama_mapel ?? '-'); ?> -
                            <?php echo e($item->kelas->nama_kelas ?? '-'); ?> -
                            <?php echo e($item->tahunAjar->tahun_ajar ?? '-'); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['mapel_kelas_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <small class="text-danger"><?php echo e($message); ?></small>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="<?php echo e(route('riwayat-kelas.index')); ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/riwayat-kelas/edit.blade.php ENDPATH**/ ?>