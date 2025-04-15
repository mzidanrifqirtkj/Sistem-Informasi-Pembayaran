<?php $__env->startSection('title_page', 'Tagihan Terjadwal'); ?>
<?php $__env->startSection('content'); ?>

    <div class="row">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_tagihan_terjadwal')): ?>
            <div class="col-md-2">
                <a href="<?php echo e(route('tagihan_terjadwal.create')); ?>" class="btn btn-primary">Buat Tagihan</a><br><br>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bulk_generate_tagihan_terjadwal')): ?>
            <div class="col-md-2">
                <a href="<?php echo e(route('tagihan_terjadwal.createBulkTerjadwal')); ?>" class="btn btn-primary">Generate
                    Tagihan</a><br><br>
            </div>
        <?php endif; ?>
        <div class="col-md-8 mb-3">
            <form action="#" class="flex-sm">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Search"
                        value="<?php echo e(Request::get('keyword')); ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary mr-2 rounded-right" type="submit"><i
                                class="fas fa-search"></i></button>
                        <button onclick="window.location.href='<?php echo e(route('tagihan_terjadwal.index')); ?>'" type="button"
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
                    <th>Nama Santri</th>
                    <th>Nama Tagihan</th>
                    <th>Tahun</th>
                    <th>Nominal</th>
                    <th>Rincian</th>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_tagihan_terjadwal')): ?>
                        <th width="13%">Action</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $tagihanTerjadwals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result => $tagihan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><a href="<?php echo e(route('santri.show', $tagihan->santri)); ?>"
                                target="blank"><?php echo e($tagihan->santri->nama_santri); ?></a></td>
                        <td><a href="<?php echo e(route('biaya_terjadwal.index')); ?>"
                                target="blank"><?php echo e($tagihan->biayaTerjadwal->nama_biaya); ?></a></td>
                        <td><?php echo e($tagihan->tahun); ?></td>
                        <td>Rp <?php echo e(number_format($tagihan->nominal, 0, ',', '.')); ?></td>
                        <td>

                            <?php if(is_array($tagihan->rincian) && !empty($tagihan->rincian)): ?>
                                <ul class="list-group">
                                    <?php $__currentLoopData = $tagihan->rincian; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item">
                                            <?php echo e($item['keterangan'] ?? $item); ?>

                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <span class="badge bg-secondary">Tidak ada rincian</span>
                            <?php endif; ?>
                        </td>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_tagihan_terjadwal')): ?>
                            <td align="center">
                                <a href="<?php echo e(route('tagihan_terjadwal.edit', $tagihan->id_tagihan_terjadwal)); ?>" type="button"
                                    class="btn btn-sm btn-info"><i class="fas fa-pen"></i></a>
                                
                                <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger"
                                    onclick="deleteData('<?php echo e($tagihan->id_tagihan_terjadwal); ?>')" data-toggle="modal"
                                    data-target="#deleteModal"><i class="fas fa-trash"></i></a>
                                
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4">Tidak ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-2 float-left">
        
    </div>
    <div class="mt-3 float-right">
        <?php echo e($tagihanTerjadwals->links('pagination::bootstrap-5')); ?>

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
                        <h4 class="modal-title" id="vcenter">Hapus Data</h4>
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
            let url = '<?php echo e(route('tagihan_terjadwal.destroy', ':id')); ?>';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
        }

        function formSubmit() {
            $("#deleteForm").submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/tagihan-terjadwal/index.blade.php ENDPATH**/ ?>