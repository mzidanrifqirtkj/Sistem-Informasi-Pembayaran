<?php $__env->startSection('title_page', 'Absensi Santri'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Absensi Santri - <?php echo e($months[$currentMonth]); ?> <?php echo e($currentYear); ?></h3>
                    </div>
                    <div class="card-body">
                        <!-- Form Filter -->
                        <form method="GET" action="<?php echo e(route('absensi.index')); ?>" class="mb-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="nama">Nama Santri</label>
                                        <input type="text" class="form-control" id="nama" name="nama"
                                            value="<?php echo e($namaSantri); ?>" placeholder="Cari nama...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="kelas">Kelas</label>
                                        <select class="form-control" id="kelas" name="kelas">
                                            <option value="">Semua Kelas</option>
                                            <?php $__currentLoopData = $kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($k->id_kelas); ?>"
                                                    <?php echo e($kelasId == $k->id_kelas ? 'selected' : ''); ?>><?php echo e($k->nama_kelas); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="bulan">Bulan</label>
                                        <select class="form-control" id="bulan" name="bulan">
                                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>"
                                                    <?php echo e($currentMonth == $key ? 'selected' : ''); ?>><?php echo e($month); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="tahun">Tahun</label>
                                        <select class="form-control" id="tahun" name="tahun">
                                            <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($year); ?>"
                                                    <?php echo e($currentYear == $year ? 'selected' : ''); ?>><?php echo e($year); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="tahun_ajar">Tahun Ajar</label>
                                        <select class="form-control" id="tahun_ajar" name="tahun_ajar">
                                            <?php $__currentLoopData = $tahunAjars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($ta->id_tahun_ajar); ?>"
                                                    <?php echo e($tahunAjar->id_tahun_ajar == $ta->id_tahun_ajar ? 'selected' : ''); ?>>
                                                    <?php echo e($ta->tahun_ajar); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="d-block">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="<?php echo e(route('absensi.laporan')); ?>" class="btn btn-info">Lihat Laporan</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <?php if(count($santris) > 0): ?>
                            <!-- Form Pengisian Absensi Hari Ini -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Isi Absensi Hari Ini (<?php echo e(\Carbon\Carbon::now()->format('d-m-Y')); ?>)</h5>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo e(route('absensi.bulk.store')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="tanggal"
                                            value="<?php echo e(\Carbon\Carbon::now()->format('Y-m-d')); ?>">
                                        <input type="hidden" name="tahun_ajar_id" value="<?php echo e($tahunAjar->id_tahun_ajar); ?>">
                                        <input type="hidden" name="kelas_id" value="<?php echo e($kelasId); ?>">

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th width="10%">NIS</th>
                                                        <th width="30%">Nama Santri</th>
                                                        <th width="25%">Kelas</th>
                                                        <th width="30%">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($key + 1); ?></td>
                                                            <td><?php echo e($santri->nis); ?></td>
                                                            <td><?php echo e($santri->nama_santri); ?></td>
                                                            <td>
                                                                <?php
                                                                    $kelasNama =
                                                                        DB::table('riwayat_kelas')
                                                                            ->join(
                                                                                'kelas',
                                                                                'riwayat_kelas.kelas_id',
                                                                                '=',
                                                                                'kelas.id_kelas',
                                                                            )
                                                                            ->where('santri_id', $santri->id_santri)
                                                                            ->where(
                                                                                'tahun_ajar_id',
                                                                                $tahunAjar->id_tahun_ajar,
                                                                            )
                                                                            ->value('nama_kelas') ?? 'Belum Ada Kelas';
                                                                ?>
                                                                <?php echo e($kelasNama); ?>

                                                            </td>
                                                            <td>
                                                                <?php
                                                                    $today = \Carbon\Carbon::now()->format('Y-m-d');
                                                                    $absensiToday =
                                                                        $absensis[$santri->nis][
                                                                            \Carbon\Carbon::now()->format('d')
                                                                        ] ?? null;
                                                                    $currentStatus = $absensiToday->status ?? '';
                                                                ?>
                                                                <div class="btn-group btn-group-toggle"
                                                                    data-toggle="buttons">
                                                                    <label
                                                                        class="btn btn-outline-success <?php echo e($currentStatus == 'hadir' ? 'active' : ''); ?>">
                                                                        <input type="radio"
                                                                            name="status[<?php echo e($santri->nis); ?>]"
                                                                            value="hadir"
                                                                            <?php echo e($currentStatus == 'hadir' ? 'checked' : ''); ?>>
                                                                        Hadir
                                                                    </label>
                                                                    <label
                                                                        class="btn btn-outline-warning <?php echo e($currentStatus == 'izin' ? 'active' : ''); ?>">
                                                                        <input type="radio"
                                                                            name="status[<?php echo e($santri->nis); ?>]"
                                                                            value="izin"
                                                                            <?php echo e($currentStatus == 'izin' ? 'checked' : ''); ?>>
                                                                        Izin
                                                                    </label>
                                                                    <label
                                                                        class="btn btn-outline-info <?php echo e($currentStatus == 'sakit' ? 'active' : ''); ?>">
                                                                        <input type="radio"
                                                                            name="status[<?php echo e($santri->nis); ?>]"
                                                                            value="sakit"
                                                                            <?php echo e($currentStatus == 'sakit' ? 'checked' : ''); ?>>
                                                                        Sakit
                                                                    </label>
                                                                    <label
                                                                        class="btn btn-outline-danger <?php echo e($currentStatus == 'alpha' ? 'active' : ''); ?>">
                                                                        <input type="radio"
                                                                            name="status[<?php echo e($santri->nis); ?>]"
                                                                            value="alpha"
                                                                            <?php echo e($currentStatus == 'alpha' ? 'checked' : ''); ?>>
                                                                        Alpha
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-right mt-3">
                                            <button type="submit" class="btn btn-primary">Simpan Absensi Hari Ini</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Tabel Absensi Bulan Ini -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5>Rekap Absensi Bulan <?php echo e($months[$currentMonth]); ?> <?php echo e($currentYear); ?></h5>
                                    <a href="<?php echo e(route('absensi.export', request()->query())); ?>"
                                        class="btn btn-sm btn-success">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" style="vertical-align: middle;">No</th>
                                                    <th rowspan="2" style="vertical-align: middle;">NIS</th>
                                                    <th rowspan="2" style="vertical-align: middle;">Nama Santri</th>
                                                    <th colspan="<?php echo e($daysInMonth); ?>" class="text-center">Tanggal</th>
                                                </tr>
                                                <tr>
                                                    <?php for($i = 1; $i <= $daysInMonth; $i++): ?>
                                                        <th class="text-center" width="30px"><?php echo e($i); ?></th>
                                                    <?php endfor; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($key + 1); ?></td>
                                                        <td><?php echo e($santri->nis); ?></td>
                                                        <td><?php echo e($santri->nama_santri); ?></td>

                                                        <?php for($i = 1; $i <= $daysInMonth; $i++): ?>
                                                            <td class="text-center">
                                                                <?php if(isset($absensis[$santri->nis][$i])): ?>
                                                                    <?php
                                                                        $status = $absensis[$santri->nis][$i]->status;
                                                                        if ($status == 'hadir') {
                                                                            $badge = 'success';
                                                                            $text = 'H';
                                                                        } elseif ($status == 'izin') {
                                                                            $badge = 'warning';
                                                                            $text = 'I';
                                                                        } elseif ($status == 'sakit') {
                                                                            $badge = 'info';
                                                                            $text = 'S';
                                                                        } else {
                                                                            $badge = 'danger';
                                                                            $text = 'A';
                                                                        }
                                                                    ?>
                                                                    <span
                                                                        class="badge badge-<?php echo e($badge); ?>"><?php echo e($text); ?></span>
                                                                <?php else: ?>
                                                                    <?php
                                                                        $date = \Carbon\Carbon::createFromDate(
                                                                            $currentYear,
                                                                            $currentMonth,
                                                                            $i,
                                                                        );
                                                                        $isWeekend =
                                                                            $date->isSaturday() || $date->isSunday();
                                                                        $isPastDate = $date->isPast();
                                                                        $isToday = $date->isToday();
                                                                    ?>

                                                                    <?php if($isPastDate && !$isWeekend && !$isToday): ?>
                                                                        <span class="badge badge-secondary"
                                                                            data-toggle="tooltip"
                                                                            title="Belum diisi">-</span>
                                                                    <?php elseif($isWeekend): ?>
                                                                        <span class="badge badge-light"
                                                                            data-toggle="tooltip" title="Weekend">W</span>
                                                                    <?php else: ?>
                                                                        <span
                                                                            class="badge
                                                                            badge-light"
                                                                            data-toggle="tooltip"
                                                                            title="Belum waktunya">-</span>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </td>
                                                        <?php endfor; ?>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Tidak ada data santri yang sesuai dengan filter yang dipilih.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function() {
                // Inisialisasi tooltip
                $('[data-toggle="tooltip"]').tooltip();

                // Tombol untuk mengisi absensi per tanggal tertentu
                $('.btn-isi-absensi').on('click', function() {
                    let tanggal = $(this).data('tanggal');
                    $('#modalTanggal').text(tanggal);
                    $('#inputTanggal').val(tanggal);
                    $('#modalAbsensi').modal('show');
                });

                // Ajax untuk menyimpan absensi
                $(document).on('change', '.radioAbsensi', function() {
                    let nis = $(this).data('nis');
                    let status = $(this).val();
                    let tanggal = $(this).data('tanggal');
                    let kelas_id = $('#kelas').val() || $(this).data('kelas');
                    let tahun_ajar_id = $('#tahun_ajar').val();

                    $.ajax({
                        url: "<?php echo e(route('absensi.store')); ?>",
                        type: "POST",
                        data: {
                            _token: "<?php echo e(csrf_token()); ?>",
                            nis: nis,
                            kelas_id: kelas_id,
                            tanggal: tanggal,
                            status: status,
                            tahun_ajar_id: tahun_ajar_id
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Terjadi kesalahan: ' + xhr.responseJSON.message);
                        }
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/absensi/index.blade.php ENDPATH**/ ?>