<?php $__env->startSection('title_page', 'Roles & Permissions'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-4">
            <a href="<?php echo e(route('roles.create')); ?>" class="btn btn-primary">Tambah Role</a>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered" id="rolesTable">
            <thead>
                <tr align="center">
                    <th width="5%">No</th>
                    <th>Nama Role</th>
                    <th>Permissions</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="mt-2 float-left">
        <span class="ml-3">Data Keseluruhan: <span
                class="text-primary font-weight-bold"><?php echo e(DB::table('roles')->count()); ?></span> role.</span>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <div class="modal fade" id="deleteRoleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="deleteForm" method="post">
                <?php echo method_field('DELETE'); ?> <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Hapus Role</h4>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin ingin menghapus role ini?</p>
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
        $(document).ready(function() {
            $('#rolesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "<?php echo e(route('roles.index')); ?>",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'permissions',
                        name: 'permissions'
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
            let url = '<?php echo e(route('roles.destroy', ':id')); ?>';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $("#deleteRoleModal").modal('show');
        }

        function formSubmit() {
            $("#deleteForm").submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/roles/index.blade.php ENDPATH**/ ?>