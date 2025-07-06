<?php $__env->startSection('title_page', 'Tambah Riwayat Kelas Santri'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('riwayat-kelas.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label for="santri_id" class="form-label">Santri</label>
                <select name="santri_id" id="santri_id" class="form-control select2" required>
                    <option value="">-- Pilih Santri --</option>
                    <?php $__currentLoopData = $santri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id_santri); ?>"><?php echo e($s->nama_santri); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="mapel_kelas_id" class="form-label">Jadwal Mapel</label>
                <select name="mapel_kelas_id" id="mapel_kelas_id" class="form-control select2" required>
                    <option value="">-- Pilih Mapel Kelas --</option>
                    <?php $__currentLoopData = $mapelKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($mk->id_mapel_kelas); ?>">
                            <?php echo e($mk->mataPelajaran->nama_mapel ?? '-'); ?> -
                            <?php echo e($mk->kelas->nama_kelas ?? '-'); ?> -
                            <?php echo e($mk->tahunAjar->tahun_ajar ?? '-'); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo e(route('riwayat-kelas.index')); ?>" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/riwayat-kelas/create.blade.php ENDPATH**/ ?>