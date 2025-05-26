<?php $__env->startSection('title_page', 'Pembayaran Santri'); ?>
<?php $__env->startSection('content'); ?>

    <?php if(Session::has('alert')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo e(Session('alert')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <div class="container">
        <h1>Tagihan untuk <?php echo e($santri->nama_santri); ?></h1>

        <h4>Tagihan Bulanan</h4>
        <table id="example" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $santri->tagihanBulanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tagihan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($tagihan->bulan); ?></td>
                        <td><?php echo e($tagihan->tahun); ?></td>
                        <td><?php echo e($tagihan->nominal); ?></td>
                        <td>
                            <span
                                class="status-checkbox <?php echo e($tagihan->status == 'lunas' ? 'text-primary' : 'text-danger'); ?>">
                                <?php echo e($tagihan->status == 'lunas' ? '✔' : '✖'); ?>

                            </span>
                        </td>

                        
                        <?php if($tagihan->status === 'lunas'): ?>
                            <td>
                                <a href="<?php echo e(route('tagihan_bulanan.edit', $tagihan->id_tagihan_bulanan)); ?>" type="button"
                                    class="btn btn-sm btn-warning"><i class="fas fa-print"></i></a>
                                
                                <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger"
                                    onclick="deleteDataBulanan('<?php echo e($tagihan->id_tagihan_bulanan); ?>')" data-toggle="modal"
                                    data-target="#deleteModal"><i class="fas fa-trash"></i></a>
                            </td>
                        <?php else: ?>
                            <td>
                                <form action="<?php echo e(route('pembayaran.store')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="santri_id" value="<?php echo e($santri->id_santri); ?>">
                                    <input type="hidden" name="jenis_tagihan" value="bulanan">
                                    <input type="hidden" name="tagihan_id" value="<?php echo e($tagihan->id_tagihan_bulanan); ?>">
                                    <input type="number" name="nominal" placeholder="Masukkan Nominal" required>
                                    <button type="submit" class="btn btn-success">Bayar</button>
                                </form>
                            </td>
                        <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <h4>Tagihan Terjadwal</h4>
        <table id="example" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Tagihan</th>
                    <th>Tahun</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $santri->tagihanTerjadwal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tagihan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($tagihan->biayaTerjadwal->nama_biaya); ?></td>
                        <td><?php echo e($tagihan->tahun); ?></td>
                        <td><?php echo e($tagihan->nominal); ?></td>
                        <td>
                            <span
                                class="status-checkbox <?php echo e($tagihan->status == 'lunas' ? 'text-primary' : 'text-danger'); ?>">
                                <?php echo e($tagihan->status == 'lunas' ? '✔' : '✖'); ?>

                            </span>
                        </td>
                        <?php if($tagihan->status === 'lunas'): ?>
                            <td>
                                <a href="<?php echo e(route('tagihan_terjadwal.edit', $tagihan->id_tagihan_terjadwal)); ?>"
                                    type="button" class="btn btn-sm btn-warning"><i class="fas fa-print"></i></a>
                                
                                <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger"
                                    onclick="deleteDataTerjadwal('<?php echo e($tagihan->id_tagihan_terjadwal); ?>')"
                                    data-toggle="modal" data-target="#deleteModal"><i class="fas fa-trash"></i></a>
                            </td>
                        <?php else: ?>
                            <td>
                                <form action="<?php echo e(route('pembayaran.store')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="santri_id" value="<?php echo e($santri->id_santri); ?>">
                                    <input type="hidden" name="jenis_tagihan" value="terjadwal">
                                    <input type="hidden" name="tagihan_id" value="<?php echo e($tagihan->id_tagihan_terjadwal); ?>">
                                    <input type="number" name="nominal" placeholder="Masukkan Nominal" required>
                                    <button type="submit" class="btn btn-success">Bayar</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteTagihanBulananModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteFormBulanan" method="post">
                <?php echo method_field('DELETE'); ?>
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter">Hapus Tagihan Bulanan</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" onclick="formSubmitBulanan()" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteTagihanTerjadwalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteFormTerjadwal" method="post">
                <?php echo method_field('DELETE'); ?>
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter">Hapus Tagihan Terjadwal</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" onclick="formSubmitTerjadwal()" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        function deleteDataBulanan(id) {
            let url = '<?php echo e(route('tagihan_bulanan.destroy', ':id')); ?>';
            url = url.replace(':id', id);
            $("#deleteFormBulanan").attr('action', url);
            $("#deleteTagihanBulananModal").modal('show');
        }

        function deleteDataTerjadwal(id) {
            let url = '<?php echo e(route('tagihan_terjadwal.destroy', ':id')); ?>';
            url = url.replace(':id', id);
            $("#deleteFormTerjadwal").attr('action', url);
            $("#deleteTagihanTerjadwalModal").modal('show');
        }

        function formSubmitBulanan() {
            $("#deleteFormBulanan").submit();
        }

        function formSubmitTerjadwal() {
            $("#deleteFormTerjadwal").submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/pembayaran/show.blade.php ENDPATH**/ ?>