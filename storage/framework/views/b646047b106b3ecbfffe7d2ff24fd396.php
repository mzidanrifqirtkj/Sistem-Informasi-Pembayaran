<?php $__env->startSection('title_page', 'Tambah Mapel Kelas'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container">
        <form action="<?php echo e(route('mapel_kelas.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <!-- Pilih Qori -->
            <div class="mb-3">
                <label for="qoriKelas" class="form-label">Qori</label>
                <select id="qoriKelas" name="qori_id" class="form-control" required>
                    <option value="">-- Pilih Qori --</option>
                    <?php $__currentLoopData = $qoriKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($qori->santri): ?>
                            <option value="<?php echo e($qori->id_qori_kelas); ?>">
                                <?php echo e($qori->santri->nama_santri); ?>

                            </option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Pilih Tahun Ajar -->
            <div class="mb-3">
                <label for="tahunAjar" class="form-label">Tahun Ajar</label>
                <select id="tahunAjar" name="tahun_ajar_id" class="form-control" required>
                    <option value="">-- Pilih Tahun Ajar --</option>
                    <?php $__currentLoopData = $tahunAjar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tahun->id_tahun_ajar); ?>">
                            <?php echo e($tahun->tahun_ajar); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Pilih Kelas -->
            <div class="mb-3">
                <label for="kelas" class="form-label">Kelas</label>
                <select id="kelas" name="kelas_id" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php $__currentLoopData = $kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->id_kelas); ?>">
                            <?php echo e($k->nama_kelas); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Jam Mulai -->
            <div class="mb-3">
                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                <input type="time" id="jam_mulai" name="jam_mulai" class="form-control" required>
            </div>

            <!-- Jam Selesai -->
            <div class="mb-3">
                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                <input type="time" id="jam_selesai" name="jam_selesai" class="form-control" required>
            </div>

            <!-- Pilih Pelajaran -->
            <div class="mb-3">
                <label for="mapel_id" class="form-label">Pilih Pelajaran</label>
                <select id="mapel_id" name="mapel_id" class="form-control" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php $__currentLoopData = $mapel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->id_mapel); ?>">
                            <?php echo e($m->nama_mapel); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo e(route('mapel_kelas.index')); ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/mapel-kelas/create.blade.php ENDPATH**/ ?>