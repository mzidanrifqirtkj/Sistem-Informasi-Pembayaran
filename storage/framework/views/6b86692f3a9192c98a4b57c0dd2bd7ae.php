<?php $__env->startSection('title_page', 'Riwayat Tagihan Syahriah'); ?>
<?php $__env->startSection('content'); ?>



    <div class="row">
        <div class="col-md-4">
            <a href="<?php echo e(route('tagihan_bulanan.create')); ?>" class="btn btn-primary">Buat Tagihan Syahriah</a><br><br>
        </div>
        <div class="col-md-4">
            <a href="<?php echo e(route('tagihan_bulanan.createBulkBulanan')); ?>" class="btn btn-primary">Generate Tagihan
                Syahriah</a><br><br>
        </div>
        <div class="col-md-4 mb-3">
            <form action="#" class="flex-sm">
                <div class="input-group">
                    <select class="form-control select2" name="year" id="year">
                        <?php for($year = (int) date('Y'); 1900 <= $year; $year--): ?>
                            <option value="<?php echo e($year); ?>" <?php if($year == $now): ?> selected <?php endif; ?>>
                                <?php echo e($year); ?>

                            </option>
                        <?php endfor; ?>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-primary mr-2 rounded-right" type="submit"><i
                                class="fas fa-search"></i></button>
                        <button onclick="window.location.href='<?php echo e(route('tagihan_bulanan.index')); ?>'" type="button"
                            class="btn btn-md btn-secondary rounded"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
                <br>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr align="center">
                    <th colspan="14"><?php echo e($now); ?></th>
                </tr>
                <tr align="center">
                    <th class="w-25">Nama Santri</th>
                    <th>Jan</th>
                    <th>Feb</th>
                    <th>Mar</th>
                    <th>Apr</th>
                    <th>May</th>
                    <th>Jun</th>
                    <th>Jul</th>
                    <th>Aug</th>
                    <th>Sep</th>
                    <th>Oct</th>
                    <th>Nov</th>
                    <th>Dec</th>
                </tr>
            </thead>
            <tbody>
                <?php if(Auth::user()->hasRole('admin')): ?>
                    <!-- Tampilkan semua data santri untuk admin -->
                    <?php $__empty_1 = true; $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr align="center">
                            <td><a href="<?php echo e(route('santri.show', $santri)); ?>" target="_blank"><?php echo e($santri->nama_santri); ?></a>
                            </td>
                            <?php
                                $months = [
                                    'Jan',
                                    'Feb',
                                    'Mar',
                                    'Apr',
                                    'May',
                                    'Jun',
                                    'Jul',
                                    'Aug',
                                    'Sep',
                                    'Oct',
                                    'Nov',
                                    'Dec',
                                ];
                                $bulanTagihan = $santri->tagihanBulanan->pluck('bulan')->toArray();
                            ?>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td>
                                    <div class="custom-control custom-checkbox" style="display: flex">
                                        <input type="checkbox" class="custom-control-input" id="cbx-<?php echo e($loop->index); ?>"
                                            disabled <?php if(in_array($month, $bulanTagihan)): ?> checked <?php endif; ?>>
                                        <label class="custom-control-label" for="cbx-<?php echo e($loop->index); ?>"></label>
                                    </div>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="14">Tidak ada data.</td>
                        </tr>
                    <?php endif; ?>
                <?php elseif(Auth::user()->hasRole('santri')): ?>
                    <!-- Tampilkan data santri yang login saja -->
                    <?php $__empty_1 = true; $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr align="center">
                            <td><a href="<?php echo e(route('santri.show', $santri)); ?>"
                                    target="_blank"><?php echo e($santri->nama_santri); ?></a></td>
                            <?php
                                $months = [
                                    'Jan',
                                    'Feb',
                                    'Mar',
                                    'Apr',
                                    'May',
                                    'Jun',
                                    'Jul',
                                    'Aug',
                                    'Sep',
                                    'Oct',
                                    'Nov',
                                    'Dec',
                                ];
                                $bulanTagihan = $santri->tagihanBulanan->pluck('bulan')->toArray();
                            ?>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td>
                                    <div class="custom-control custom-checkbox" style="display: flex">
                                        <input type="checkbox" class="custom-control-input" id="cbx-<?php echo e($loop->index); ?>"
                                            disabled <?php if(in_array($month, $bulanTagihan)): ?> checked <?php endif; ?>>
                                        <label class="custom-control-label" for="cbx-<?php echo e($loop->index); ?>"></label>
                                    </div>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="14">Tidak ada data.</td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-2 float-left">
        <span class="ml-3">Data Keseluruhan: <span
                class="text-primary font-weight-bold"><?php echo e(DB::table('santris')->count()); ?></span> Santri</span>
    </div>
    <div class="mt-3 float-right">
        <?php echo e($santris->links('pagination::bootstrap-5')); ?>

    </div>

    <br><br><br>
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-8">
                <h4>Riwayat Tagihan</h4>
            </div>
            <div class="col-md-4">
                <form action="#" class="flex-sm">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Search"
                            value="<?php echo e(Request::get('keyword')); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-primary mr-2 rounded-right" type="submit"><i
                                    class="fas fa-search"></i></button>
                            <button onclick="window.location.href='<?php echo e(route('tagihan_bulanan.index')); ?>'" type="button"
                                class="btn btn-md btn-secondary rounded"><i class="fas fa-sync-alt"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr align="center">
                        <th>Nama</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Nominal</th>
                        <th>Rincian</th>
                        <th width="13%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(Auth::user()->hasRole('admin')): ?>
                        <!-- Tampilkan semua data tagihan untuk admin -->
                        <?php $__empty_1 = true; $__currentLoopData = $dataTagihans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><a href="<?php echo e(route('santri.show', $result->santri)); ?>"
                                        target="blank"><?php echo e($result->santri->nama_santri); ?></a></td>
                                <td><?php echo e($result->bulan); ?></td>
                                <td><?php echo e($result->tahun); ?></td>
                                <td><?php echo e($result->nominal); ?></td>
                                <td><?php echo e(json_encode($result->rincian)); ?></td>
                                <td align="center">
                                    <a href="<?php echo e(route('tagihan_bulanan.edit', $result->id_tagihan_bulanan)); ?>"
                                        type="button" class="btn btn-sm btn-warning"><i class="fas fa-print"></i></a>
                                    <a href="javascript:void(0)" id="btn-delete" class="btn btn-sm btn-danger"
                                        onclick="deleteData('<?php echo e($result->id_tagihan_bulanan); ?>')" data-toggle="modal"
                                        data-target="#deleteSyahriahModal"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7">Tidak ada data.</td>
                            </tr>
                        <?php endif; ?>
                    <?php elseif(Auth::user()->hasRole('santri')): ?>
                        <!-- Tampilkan data tagihan untuk santri yang login -->
                        <?php $__empty_1 = true; $__currentLoopData = $dataTagihans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><a href="<?php echo e(route('santri.show', $result->santri)); ?>"
                                        target="blank"><?php echo e($result->santri->nama_santri); ?></a></td>
                                <td><?php echo e($result->bulan); ?></td>
                                <td><?php echo e($result->tahun); ?></td>
                                <td><?php echo e($result->nominal); ?></td>
                                <td><?php echo e(json_encode($result->rincian)); ?></td>
                                <td align="center">
                                    <a href="<?php echo e(route('tagihan_bulanan.edit', $result->id_tagihan_bulanan)); ?>"
                                        type="button" class="btn btn-sm btn-warning"><i class="fas fa-print"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7">Tidak ada data.</td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="deleteSyahriahModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="javascript:void(0)" id="deleteForm" method="post">
                <?php echo method_field('DELETE'); ?>
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter">Hapus Data</h4>
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
        function deleteData(id) {
            let url = '<?php echo e(route('tagihan_bulanan.destroy', ':id')); ?>';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
        }

        function formSubmit() {
            $("#deleteForm").submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/tagihan-bulanan/index.blade.php ENDPATH**/ ?>