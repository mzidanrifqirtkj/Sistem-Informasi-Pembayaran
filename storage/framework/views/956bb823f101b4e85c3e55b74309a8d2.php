<?php $__env->startSection('title_page', 'Riwayat Kelas'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container">
        <a href="<?php echo e(route('riwayat-kelas.create')); ?>" class="btn btn-primary mb-3">
            + Tambah Riwayat Kelas
        </a>
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="filterKelas" class="form-control select2">
                    <option value="">-- Pilih Kelas --</option>
                    <?php $__currentLoopData = $kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id_kelas); ?>"><?php echo e($item->nama_kelas); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-md-3">
                <select id="filterMapel" class="form-control select2">
                    <option value="">-- Pilih Mapel --</option>
                    <?php $__currentLoopData = $mapel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id_mapel); ?>"><?php echo e($item->nama_mapel); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-md-3">
                <select id="filterTahun" class="form-control select2">
                    <option value="">-- Pilih Tahun Ajar --</option>
                    <?php $__currentLoopData = $tahunAjar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id_tahun_ajar); ?>"><?php echo e($item->tahun_ajar); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-md-3">
                <button id="clearFilter" class="btn btn-secondary w-100">
                    Hapus Filter
                </button>
            </div>

        </div>

        <table id="riwayatTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Santri</th>
                    <th>Kelas</th>
                    <th>Mapel</th>
                    <th>Tahun Ajar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(function() {
            const table = $('#riwayatTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('riwayat-kelas.data')); ?>',
                    data: function(d) {
                        d.kelas_id = $('#filterKelas').val();
                        d.mapel_id = $('#filterMapel').val();
                        d.tahun_ajar_id = $('#filterTahun').val();
                    }
                },
                columns: [{
                        data: 'santri',
                        name: 'santri'
                    },
                    {
                        data: 'kelas',
                        name: 'kelas'
                    },
                    {
                        data: 'mapel',
                        name: 'mapel'
                    },
                    {
                        data: 'tahun_ajar',
                        name: 'tahun_ajar'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },

                ]
            });

            $('#filterKelas, #filterMapel, #filterTahun').change(function() {
                table.ajax.reload();
            });
        });

        $('#clearFilter').click(function() {
            $('#filterKelas').val('').trigger('change');
            $('#filterMapel').val('').trigger('change');
            $('#filterTahun').val('').trigger('change');
            $('#riwayatTable').DataTable().ajax.reload();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/riwayat-kelas/index.blade.php ENDPATH**/ ?>