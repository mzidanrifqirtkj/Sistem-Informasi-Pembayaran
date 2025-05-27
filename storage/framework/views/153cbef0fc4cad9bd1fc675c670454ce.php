<?php $__env->startSection('title_page', 'Edit Daftar Biaya'); ?>

<?php $__env->startSection('css_inline'); ?>
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .input-group-text {
            background-color: #e9ecef;
        }

        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Daftar Biaya</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('daftar-biayas.update', $daftarBiaya->id_daftar_biaya)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <div class="form-group row">
                                <label for="kategori_biaya" class="col-sm-3 col-form-label">Kategori Biaya</label>
                                <div class="col-sm-9">
                                    <input type="text" name="kategori_biaya" id="kategori_biaya" class="form-control"
                                        value="<?php echo e(old('kategori_biaya', $daftarBiaya->kategoriBiaya->nama_kategori ?? '')); ?>"
                                        required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nominal" class="col-sm-3 col-form-label">Nominal</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" name="nominal" id="nominal" class="form-control"
                                            value="<?php echo e($daftarBiaya->nominal); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                    <a href="<?php echo e(route('daftar-biayas.index')); ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            // Filter kategori based on selected status
            $('#status').change(function() {
                var status = $(this).val();
                $.ajax({
                    url: '<?php echo e(route('daftar-biayas.get-categories')); ?>',
                    type: 'GET',
                    data: {
                        status: status
                    },
                    success: function(data) {
                        $('#kategori_biaya_id').empty();
                        $('#kategori_biaya_id').append(
                            '<option value="">Pilih Kategori</option>');
                        $.each(data, function(key, value) {
                            $('#kategori_biaya_id').append(
                                '<option value="' + value.id_kategori_biaya + '">' +
                                value.nama_kategori + '</option>'
                            );
                        });
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/daftar-biayas/edit.blade.php ENDPATH**/ ?>