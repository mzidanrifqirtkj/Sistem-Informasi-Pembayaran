<?php $__env->startSection('title_page', 'Pembayaran Santri'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-8">
                <h1>Pilih Santri untuk Membayar Tagihan</h1>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2 justify-content-end">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pembayaran-bulk')): ?>
                        <a href="<?php echo e(route('pembayaran.bulk.index')); ?>" class="btn btn-success">
                            <i class="fas fa-users"></i> Pembayaran Massal
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="<?php echo e(route('pembayaran.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Cari NIS atau Nama Santri"
                            value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="kategori" class="form-control select2">
                            <option value="">-- Semua Kategori --</option>
                            <?php $__currentLoopData = \App\Models\KategoriBiaya::where('status', 'jalur')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($kategori->id_kategori_biaya); ?>"
                                    <?php echo e(request('kategori') == $kategori->id_kategori_biaya ? 'selected' : ''); ?>>
                                    <?php echo e($kategori->nama_kategori); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="<?php echo e(route('pembayaran.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="santriTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIS</th>
                                <th>Nama</th>
                                <th width="20%">Kategori</th>
                                <th width="15%">Total Tunggakan</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    // FIXED: Gunakan accessor baru yang sudah menghitung dengan benar
                                    $totalTunggakan = $santri->total_tunggakan;
                                ?>
                                <tr>
                                    <td><?php echo e($santris->firstItem() + $index); ?></td>
                                    <td><?php echo e($santri->nis); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('santri.show', $santri->id_santri)); ?>" target="_blank">
                                            <?php echo e($santri->nama_santri); ?>

                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo e($santri->kategori_biaya_utama_name); ?>

                                        </span>
                                        <?php if($santri->all_kategori_biaya->count() > 1): ?>
                                            <br><small class="text-muted">
                                                + <?php echo e($santri->all_kategori_biaya->count() - 1); ?> kategori lain
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <?php if($totalTunggakan > 0): ?>
                                            <span class="text-danger font-weight-bold">
                                                Rp <?php echo e(number_format($totalTunggakan, 0, ',', '.')); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-success">
                                                <i class="fas fa-check-circle"></i> Lunas
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('pembayaran.show', $santri->id_santri)); ?>"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-money-bill-wave"></i> Bayar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data santri aktif</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan <?php echo e($santris->firstItem() ?? 0); ?> - <?php echo e($santris->lastItem() ?? 0); ?>

                        dari <?php echo e($santris->total()); ?> santri
                    </div>
                    <div>
                        <?php echo e($santris->links('pagination::bootstrap-5')); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/pembayaran/index.blade.php ENDPATH**/ ?>