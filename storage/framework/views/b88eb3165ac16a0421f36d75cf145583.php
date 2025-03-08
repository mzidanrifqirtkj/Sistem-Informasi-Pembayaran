<?php $__env->startSection('title_page', 'Data Absensi'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-4 d-flex justify-content-between">
            <?php if(Auth::user()->hasRole('admin')): ?>
                <a href="<?php echo e(route('absensi.importForm')); ?>" class="btn btn-primary">Import Absensi</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row align-items-end mb-3 mt-3">

        <div class="col-md-2">
            <label for="filterNama">Nama Santri</label>
            <input placeholder="Input Nama Santri" id="filterNama" name="filterName" id="filterName"
                class="form-control filterNama"></input>
        </div>

        <div class="col-md-2">
            <label for="filterKelas">Kelas</label>
            <select id="filterKelas" class="form-control">
                <option value="">Pilih Kelas</option>
                <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($kelas->id_kelas); ?>"><?php echo e($kelas->nama_kelas); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="filterTahunAjar">Tahun Ajar</label>
            <select id="filterTahunAjar" class="form-control">
                <option value="">Pilih Tahun Ajar</option>
                <?php $__currentLoopData = $tahunAjarList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($tahun->id_tahun_ajar); ?>"><?php echo e($tahun->tahun_ajar); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="filterBulan">Bulan</label>
            <select id="filterBulan" class="form-control">
                <option value="">Pilih Bulan</option>
                <option value="Jan">Januari</option>
                <option value="Feb">Februari</option>
                <option value="Mar">Maret</option>
                <option value="Apr">April</option>
                <option value="May">Mei</option>
                <option value="Jun">Juni</option>
                <option value="Jul">Juli</option>
                <option value="Aug">Agustus</option>
                <option value="Sep">September</option>
                <option value="Oct">Oktober</option>
                <option value="Nov">November</option>
                <option value="Dec">Desember</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="filterMinggu">Minggu</label>
            <select id="filterMinggu" class="form-control">
                <option value="">Pilih Minggu</option>
                <?php $__currentLoopData = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $minggu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($minggu); ?>"><?php echo e($minggu); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2 text-right">
            <button id="resetFilters" class="btn btn-secondary">Reset</button>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered" id="absensiTable">
            <thead>
                <tr align="center">
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Santri</th>
                    <th>Bulan</th>
                    <th>Minggu</th>
                    <th>Hadir</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Alpha</th>
                    <th>Kelas</th>
                    <th>Tahun Ajar</th>
                    <?php if(Auth::user()->hasRole('admin')): ?>
                        <th width="13%">Action</th>
                    <?php endif; ?>
                </tr>
            </thead>
        </table>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <?php if(Auth::user()->hasRole('admin')): ?>
        <!-- Modal Delete -->
        <div class="modal fade" id="deleteAbsensiModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="deleteForm" method="post">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Hapus Absensi</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin menghapus data absensi ini?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            let table = $('#absensiTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: "<?php echo e(route('absensi.data')); ?>",
                    type: "GET",
                    data: function(d) {
                        // Kirim nilai filter ke server
                        d.kelas = $('#filterKelas').val();
                        d.tahun_ajar = $('#filterTahunAjar').val();
                        d.bulan = $('#filterBulan').val();
                        d.minggu = $('#filterMinggu').val();
                        d.nama_santri = $('#filterNama').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'nis',
                        name: 'nis'
                    },
                    {
                        data: 'nama_santri',
                        name: 'nama_santri',
                    },
                    {
                        data: 'bulan',
                        name: 'bulan',
                        render: function(data, type, row) {
                            // Ubah nilai singkat bulan menjadi nama bulan lengkap
                            const bulanMap = {
                                'Jan': 'Januari',
                                'Feb': 'Februari',
                                'Mar': 'Maret',
                                'Apr': 'April',
                                'May': 'Mei',
                                'Jun': 'Juni',
                                'Jul': 'Juli',
                                'Aug': 'Agustus',
                                'Sep': 'September',
                                'Oct': 'Oktober',
                                'Nov': 'November',
                                'Dec': 'Desember'
                            };
                            return bulanMap[data] || data;
                        }
                    },
                    {
                        data: 'minggu_per_bulan',
                        name: 'minggu_per_bulan'
                    },
                    {
                        data: 'jumlah_hadir',
                        name: 'jumlah_hadir'
                    },
                    {
                        data: 'jumlah_izin',
                        name: 'jumlah_izin'
                    },
                    {
                        data: 'jumlah_sakit',
                        name: 'jumlah_sakit'
                    },
                    {
                        data: 'jumlah_alpha',
                        name: 'jumlah_alpha'
                    },
                    {
                        data: 'kelas',
                        name: 'kelas'
                    },
                    {
                        data: 'tahun_ajar',
                        name: 'tahun_ajar'
                    },
                    <?php if(Auth::user()->hasRole('admin')): ?>
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    <?php endif; ?>
                ],
                order: [
                    [0, 'asc']
                ], // Default pengurutan berdasarkan kolom pertama (No)
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json" // Bahasa Indonesia
                }
            });

            // Event listener untuk filter dropdown
            $('#filterKelas, #filterTahunAjar, #filterBulan, #filterMinggu').change(function() {
                table.draw(); // Memanggil ulang DataTable dengan filter baru
            });

            // Reset filter
            $('#resetFilters').click(function() {
                $('#filterKelas, #filterTahunAjar, #filterBulan, #filterMinggu, #filterNama').val('');
                table.draw(); // Memanggil ulang DataTable tanpa filter
            });

            // Fungsi untuk menghapus data
            function deleteData(id) {
                let url = '<?php echo e(route('absensi.destroy', ':id')); ?>';
                url = url.replace(':id', id);
                $("#deleteForm").attr('action', url);
                $('#deleteAbsensiModal').modal('show');
            }

            // Event listener untuk tombol hapus
            $(document).on('click', '.btn-danger', function() {
                let id = $(this).data('id');
                deleteData(id);
            });

            $('#filterNama').on('input', function() {
                table.draw(); // Memanggil ulang DataTable dengan filter baru
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/absensi/index.blade.php ENDPATH**/ ?>