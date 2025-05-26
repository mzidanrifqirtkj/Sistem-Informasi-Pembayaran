<?php $__env->startSection('title_page', 'Data Qori Kelas'); ?>


<?php $__env->startSection('content'); ?>
    <div class="container">
        <form action="<?php echo e(route('qori_kelas.generate')); ?>" method="POST" class="mb-3">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-primary" id="generateBtn">
                <i class="fas fa-sync-alt"></i> Generate Qori dari Data Santri
            </button>
        </form>

        <table class="table" id="qoriTable">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $qoriKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr id="row-<?php echo e($qori->id); ?>">
                        <td><?php echo e($qori->nis); ?></td>
                        <td><?php echo e($qori->santri->nama_santri ?? '-'); ?></td>
                        <td>
                            <span class="badge badge-<?php echo e($qori->status == 'Aktif' ? 'success' : 'danger'); ?>">
                                <?php echo e(ucfirst($qori->status)); ?>

                            </span>
                        </td>
                        <td>
                            <!-- Form Status -->

                            <form action="<?php echo e(route('qori_kelas.toggle-status', $qori->id_qori_kelas)); ?>" method="POST"
                                class="d-inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="status"
                                    value="<?php echo e($qori->status === 'Aktif' ? 'Tidak Aktif' : 'Aktif'); ?>">
                                <button type="submit"
                                    class="btn btn-sm <?php echo e($qori->status === 'Aktif' ? 'btn-outline-danger' : 'btn-outline-success'); ?>"
                                    onclick="return confirm('Apakah Anda yakin ingin mengubah status?')">
                                    Ubah Status
                                </button>
                            </form>

                            <!-- Form hapus -->

                            <form action="<?php echo e(route('qori_kelas.destroy', $qori->id_qori_kelas)); ?>" method="POST"
                                class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>

                        </td>
                        
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/qori_kelas/index.blade.php ENDPATH**/ ?>