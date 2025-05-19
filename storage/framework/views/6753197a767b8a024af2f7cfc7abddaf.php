<?php $__env->startSection('title_page', 'Biaya Bulanan Sesuai Kategori Santri'); ?>
<?php $__env->startSection('content'); ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?php echo e(route('kategori.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Tambah Kategori Bulanan
        </a>
    </div>

    <div class="row">
        <!-- Kategori Bulanan Card -->
        <div class="col-12">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Kategori Bulanan</h4>
                    <span class="badge bg-light text-info"><?php echo e($kategoriSantri->count()); ?> Kategori</span>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $kategoriSantri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 font-weight-bold"><?php echo e($kategori->nama_kategori); ?></h5>
                                <small class="text-muted">Biaya bulanan</small>
                            </div>
                            <div class="text-right">
                                <h4 class="text-success mb-1">Rp
                                    <?php echo e(number_format($kategori->nominal_syahriyah, 0, ',', '.')); ?></h4>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('kategori.edit', $kategori->id_kategori_santri)); ?>"
                                        class="btn btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger"
                                        onclick="deleteKategoriSantri('<?php echo e($kategori->id_kategori_santri); ?>')"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>Tidak ada data kategori bulanan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteKategoriModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteFormKategori" method="post">
                <?php echo method_field('DELETE'); ?>
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" onclick="formSubmitKategori()" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        function deleteKategoriSantri(id) {
            let url = '<?php echo e(route('kategori.destroy', ':id')); ?>';
            url = url.replace(':id', id);
            $("#deleteFormKategori").attr('action', url);
            $("#deleteKategoriModal").modal('show');
        }

        function formSubmitKategori() {
            $("#deleteFormKategori").submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/kategori-santri/index.blade.php ENDPATH**/ ?>