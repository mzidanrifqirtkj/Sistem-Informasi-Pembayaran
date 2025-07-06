<?php $__env->startSection('title_page', 'Data Santri'); ?>
<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-4 d-flex justify-content-between">
            <a href="<?php echo e(route('santri.create')); ?>" class="btn btn-primary">Tambah Santri</a>
            <a href="<?php echo e(route('santri.importForm')); ?>" class="btn btn-primary">Import Santri</a>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered" id="santriTable">
            <thead>
                <tr align="center">
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. HP</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
        </table>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteSantriModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="deleteForm" method="post">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Hapus Santri</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus santri ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            $('#santriTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "<?php echo e(route('santri.data')); ?>",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nis',
                        name: 'nis'
                    },
                    {
                        data: 'nama_santri',
                        name: 'nama_santri',
                        render: function(data, type, row) {
                            return '<a href="' + '<?php echo e(route('santri.show', ':id')); ?>'.replace(
                                ":id", row.id_santri) + '">' + data + '</a>';
                        }
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'no_hp',
                        name: 'no_hp'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        function deleteData(id) {
            let url = '<?php echo e(route('santri.destroy', ':id')); ?>';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $('#deleteSantriModal').modal('show');
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/santri/index.blade.php ENDPATH**/ ?>