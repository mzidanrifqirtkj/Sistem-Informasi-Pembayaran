<?php $__env->startSection('title_page', 'Data User'); ?>
<?php $__env->startSection('content'); ?>


    <div class="row">
        <div class="col-md-4 d-flex justify-content-between">
            <a href="<?php echo e(route('user.create')); ?>" class="btn btn-primary">Tambah user</a>
            <a href="<?php echo e(route('user.importForm')); ?>" class="btn btn-primary">Import user</a>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered" id="userTable">
            <thead>
                <tr align="center">
                    <th width="5%">No</th>
                    <th>Nama Santri</th>
                    <th>Email</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="mt-2 float-left">
        <span class="ml-3">Data Keseluruhan: <span
                class="text-primary font-weight-bold"><?php echo e(DB::table('users')->count()); ?></span> user.</span>
    </div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="deleteForm" method="post">
                <?php echo method_field('DELETE'); ?>
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter">Hapus user</h4>
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
        $(document).ready(function() {
            $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "<?php echo e(route('user.data')); ?>",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'santri',
                        name: 'santri'
                    },
                    {
                        data: 'email',
                        name: 'email'
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

        function viewFile(data) {
            let url = window.location.origin + '/storage/in-mail/' + data;
            $('#embed-file').attr('src', url);
        }

        function deleteData(user) {
            let url = '<?php echo e(route('user.destroy', ':user')); ?>';
            url = url.replace(':user', user);
            $("#deleteForm").attr('action', url);
            $("#deleteUserModal").modal('show');
        }



        function formSubmit() {
            $("#deleteForm").submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/user/index.blade.php ENDPATH**/ ?>