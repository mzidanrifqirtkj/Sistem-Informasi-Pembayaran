<?php $__env->startSection('title_page', isset($edit) ? 'Edit Daftar Biaya' : 'Tambah Daftar Biaya'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Tambah Daftar Biaya</h2>
                <form action="<?php echo e(route('daftar-biayas.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="status">Status Kategori</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Pilih Status</option>
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>"><?php echo e(ucfirst($status)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kategori_biaya_id">Kategori Biaya</label>
                        <select name="kategori_biaya_id" id="kategori_biaya_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($kategori->id_kategori_biaya); ?>" data-status="<?php echo e($kategori->status); ?>">
                                    <?php echo e($kategori->nama_kategori); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input type="number" name="nominal" id="nominal" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?php echo e(route('daftar-biayas.index')); ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            // Filter categories based on selected status
            $('#status').change(function() {
                var selectedStatus = $(this).val();
                if (selectedStatus) {
                    $('#kategori_biaya_id option').each(function() {
                        var $option = $(this);
                        // Show only options with matching status or the default option
                        if ($option.data('status') === selectedStatus || $option.val() === '') {
                            $option.show();
                        } else {
                            $option.hide();
                        }
                    });
                    // Reset selection
                    $('#kategori_biaya_id').val('');
                } else {
                    // Show all options if no status is selected
                    $('#kategori_biaya_id option').show();
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/daftar-biayas/create.blade.php ENDPATH**/ ?>