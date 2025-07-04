<?php $__env->startSection('title_page', 'Kategori Biaya'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container mt-1">
        <a href="<?php echo e(route('kategori-biayas.create')); ?>" class="btn btn-primary mb-3">+ Tambah Kategori</a>

        <table class="table table-bordered table-striped" id="kategoriTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $kategoriBiayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e(ucfirst($kategori->nama_kategori)); ?></td>
                        <td><?php echo e(ucfirst($kategori->status)); ?></td>
                        <td>
                            <a href="<?php echo e(route('kategori-biayas.edit', $kategori->id_kategori_biaya)); ?>"
                                class="btn btn-sm btn-warning">Edit</a>
                            <form action="<?php echo e(route('kategori-biayas.destroy', $kategori->id_kategori_biaya)); ?>" method="POST"
                                class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            $('#kategoriTable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Data kosong",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/kategori-biayas/index.blade.php ENDPATH**/ ?>