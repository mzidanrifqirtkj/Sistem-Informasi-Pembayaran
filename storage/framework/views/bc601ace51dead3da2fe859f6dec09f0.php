<?php $__env->startSection('title_page', 'Tambah Mata Pelajaran Kelas'); ?>

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

        <?php if(session('error')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('mapel_kelas.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label for="kelas_id" class="form-label">Kelas</label>
                <select name="kelas_id" id="kelas_id" class="form-control select2" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php $__currentLoopData = $kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->id_kelas); ?>" <?php echo e(old('kelas_id') == $k->id_kelas ? 'selected' : ''); ?>>
                            <?php echo e($k->nama_kelas); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="tahun_ajar_id" class="form-label">Tahun Ajar</label>
                <select name="tahun_ajar_id" id="tahun_ajar_id" class="form-control select2" required>
                    <option value="">-- Pilih Tahun Ajar --</option>
                    <?php $__currentLoopData = $tahunAjar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ta->id_tahun_ajar); ?>"
                            <?php echo e(old('tahun_ajar_id') == $ta->id_tahun_ajar ? 'selected' : ''); ?>>
                            <?php echo e($ta->tahun_ajar); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="mapel_id" class="form-label">Mata Pelajaran</label>
                <select name="mapel_id" id="mapel_id" class="form-control select2" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php $__currentLoopData = $mapel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->id_mapel); ?>" <?php echo e(old('mapel_id') == $m->id_mapel ? 'selected' : ''); ?>>
                            <?php echo e($m->nama_mapel); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="qori_id" class="form-label">Qori</label>
                <select name="qori_id" id="qori_id" class="form-control select2" required>
                    <option value="">-- Pilih Qori --</option>
                    <?php $__currentLoopData = $qoriKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($q->id_qori_kelas); ?>"
                            <?php echo e(old('qori_id') == $q->id_qori_kelas ? 'selected' : ''); ?>>
                            <?php echo e($q->santri->nama_santri ?? 'N/A'); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" value="<?php echo e(old('jam_mulai')); ?>"
                    required>
            </div>

            <div class="mb-3">
                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                <input type="time" class="form-control" id="jam_selesai" name="jam_selesai"
                    value="<?php echo e(old('jam_selesai')); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo e(route('mapel_kelas.index')); ?>" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/mapel-kelas/create.blade.php ENDPATH**/ ?>