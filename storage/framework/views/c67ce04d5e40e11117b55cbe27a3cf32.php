<?php $__env->startSection('title_page', 'Daftar Penugasan'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container my-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2>Daftar Penugasan Ustadz</h2>
            </div>
        </div>

        <!-- Tombol Navigasi Penugasan -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-center ">
                <a href="<?php echo e(route('ustadz.penugasan.qori.create')); ?>" class="btn btn-primary">Penugasan Qori</a>
                <a href="<?php echo e(route('ustadz.penugasan.mustahiq.create')); ?>" class="btn btn-primary">Penugasan Mustahiq</a>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4 offset-md-4 text-center">
                <label for="tahunAjar">Pilih Tahun Ajar</label>
                <select id="tahunAjar" class="form-control">
                    <?php $__currentLoopData = $tahunAjar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tahun->id_tahun_ajar); ?>"
                            <?php echo e(isset($defaultTahun) && $defaultTahun->id_tahun_ajar == $tahun->id_tahun_ajar ? 'selected' : ''); ?>>
                            <?php echo e($tahun->tahun_ajar); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <!-- Tabel Wali Kelas (Mustahiq) -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Daftar Wali Kelas (Mustahiq)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="waliTable" class="table table-hover table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:5%;">No</th>
                                        <th>Kelas</th>
                                        <th>Mustahiq</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data dimuat melalui AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Qori -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">Daftar Qori</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="qoriTable" class="table table-hover table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:5%;">No</th>
                                        <th>Qori</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data dimuat melalui AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            // Ambil nilai default tahun ajar dari dropdown
            let tahunAjarId = $('#tahunAjar').val();

            // Inisialisasi DataTable untuk Wali Kelas
            let waliTable = $('#waliTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('ustadz.penugasan.getWaliKelas')); ?>",
                    type: 'GET',
                    data: function(d) {
                        d.id_tahun_ajar = $('#tahunAjar').val();
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'kelas.nama_kelas',
                        name: 'nama_kelas',
                        defaultContent: '-'
                    },
                    {
                        data: 'ustadz.nama_santri',
                        name: 'nama_ustadz',
                        defaultContent: '-'
                    }
                ]
            });

            // Inisialisasi DataTable untuk Qori
            let qoriTable = $('#qoriTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('ustadz.penugasan.getQori')); ?>",
                    type: 'GET',
                    data: function(d) {
                        d.id_tahun_ajar = $('#tahunAjar').val();
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'ustadz.nama_santri',
                        name: 'nama_ustadz',
                        defaultContent: '-'
                    },
                    {
                        data: 'mapel_kelas.mata_pelajaran.nama_mapel',
                        name: 'nama_mapel',
                        defaultContent: '-'
                    },
                    {
                        data: 'mapel_kelas.kelas.nama_kelas',
                        name: 'nama_kelas',
                        defaultContent: '-'
                    }
                ]
            });

            // Reload tabel ketika dropdown tahun ajar berubah
            $('#tahunAjar').change(function() {
                waliTable.ajax.reload();
                qoriTable.ajax.reload();
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/ustadz/penugasan/index.blade.php ENDPATH**/ ?>