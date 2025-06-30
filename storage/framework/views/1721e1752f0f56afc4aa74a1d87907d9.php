<?php $__env->startSection('title_page', 'Data Mapel Kelas'); ?>
<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-2">
            <a href="<?php echo e(route('mapel_kelas.create')); ?>" class="btn btn-primary">Tambah Mapel Kelas</a><br><br>
        </div>

        <div class="col-md-8 mb-3">
            <form action="#" class="flex-sm">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Search"
                        value="<?php echo e(Request::get('keyword')); ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary mr-2 rounded-right" type="submit"><i
                                class="fas fa-search"></i></button>
                        <button onclick="window.location.href='<?php echo e(route('mapel_kelas.index')); ?>'" type="button"
                            class="btn btn-md btn-secondary rounded"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr align="center">
                    <th width="5%">No</th>
                    <th>Mata Pelajaran</th>
                    <th>Qori</th>
                    <th>Kelas</th>
                    <th>Tahun Ajar</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $mapelKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mapel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($mapel->mataPelajaran->nama_mapel ?? '-'); ?></td>
                        <td><?php echo e($mapel->qoriKelas->santri->nama_santri ?? '-'); ?></td>
                        <td><?php echo e($mapel->kelas->nama_kelas ?? '-'); ?></td>
                        <td><?php echo e($mapel->tahunAjar->tahun_ajar ?? '-'); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($mapel->jam_mulai)->format('H:i')); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($mapel->jam_selesai)->format('H:i')); ?></td>
                        <td align="center">
                            <a href="<?php echo e(route('mapel_kelas.edit', $mapel->id_mapel_kelas)); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger"
                                onclick="deleteData('<?php echo e($mapel->id_mapel_kelas); ?>')" data-toggle="modal"
                                data-target="#deleteModal">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8">Tidak ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteForm" method="post">
                <?php echo method_field('DELETE'); ?>
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Hapus Mapel Kelas</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" onclick="formSubmit()" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        function deleteData(id) {
            let url = '<?php echo e(route('mapel_kelas.destroy', ':id')); ?>';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
        }

        function formSubmit() {
            $("#deleteForm").submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/mapel-kelas/index.blade.php ENDPATH**/ ?>