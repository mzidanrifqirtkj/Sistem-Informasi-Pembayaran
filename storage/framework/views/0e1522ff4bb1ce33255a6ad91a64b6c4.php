<?php $__env->startSection('title_page', 'Kwitansi Pembayaran'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>Kwitansi Pembayaran</h1>
                    <div>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                        <a href="<?php echo e(route('pembayaran.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card" id="printArea">
                    <div class="card-body">
                        <?php if($isReprint): ?>
                            <div class="watermark">COPY</div>
                        <?php endif; ?>

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h3>KWITANSI PEMBAYARAN</h3>
                            <p class="mb-0">No: <strong><?php echo e($pembayaran->receipt_number); ?></strong></p>
                        </div>

                        <!-- Info Pembayaran -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">Telah Terima Dari</td>
                                        <td>:
                                            <?php echo e($pembayaran->tagihanBulanan->santri->nama_santri ?? $pembayaran->tagihanTerjadwal->santri->nama_santri); ?>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>NIS</td>
                                        <td>:
                                            <?php echo e($pembayaran->tagihanBulanan->santri->nis ?? $pembayaran->tagihanTerjadwal->santri->nis); ?>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kategori</td>
                                        <td>:
                                            <?php echo e($pembayaran->tagihanBulanan->santri->kategoriSantri->nama_kategori ?? $pembayaran->tagihanTerjadwal->santri->kategoriSantri->nama_kategori); ?>

                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">Tanggal Bayar</td>
                                        <td>: <?php echo e(\Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y')); ?>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jam</td>
                                        <td>: <?php echo e($pembayaran->created_at->format('H:i:s')); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Petugas</td>
                                        <td>: <?php echo e($pembayaran->createdBy->name ?? 'System'); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Nominal -->
                        <div class="text-center mb-4 p-3 bg-light">
                            <h4 class="mb-1">Jumlah Pembayaran:</h4>
                            <h2 class="text-primary mb-0">Rp
                                <?php echo e(number_format($pembayaran->nominal_pembayaran, 0, ',', '.')); ?></h2>
                            <p class="mb-0"><em><?php echo e(ucwords(terbilang($pembayaran->nominal_pembayaran))); ?> Rupiah</em></p>
                        </div>

                        <!-- Detail Pembayaran -->
                        <h5>Untuk Pembayaran:</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Keterangan</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($pembayaran->payment_type == 'allocated' && $pembayaran->paymentAllocations->count() > 0): ?>
                                        <?php $__currentLoopData = $pembayaran->paymentAllocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $allocation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td>
                                                    <?php if($allocation->tagihan_bulanan_id): ?>
                                                        Syahriah <?php echo e($allocation->tagihanBulanan->bulan); ?>

                                                        <?php echo e($allocation->tagihanBulanan->tahun); ?>

                                                    <?php else: ?>
                                                        <?php echo e($allocation->tagihanTerjadwal->daftarBiaya->kategoriBiaya->nama_kategori ?? 'Tagihan Terjadwal'); ?>

                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right">Rp
                                                    <?php echo e(number_format($allocation->allocated_amount, 0, ',', '.')); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <?php if($pembayaran->tagihan_bulanan_id): ?>
                                                    Syahriah <?php echo e($pembayaran->tagihanBulanan->bulan); ?>

                                                    <?php echo e($pembayaran->tagihanBulanan->tahun); ?>

                                                <?php else: ?>
                                                    <?php echo e($pembayaran->tagihanTerjadwal->daftarBiaya->kategoriBiaya->nama_kategori ?? 'Tagihan Terjadwal'); ?>

                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">Rp
                                                <?php echo e(number_format($pembayaran->nominal_pembayaran, 0, ',', '.')); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if($pembayaran->payment_note): ?>
                            <p><strong>Catatan:</strong> <?php echo e($pembayaran->payment_note); ?></p>
                        <?php endif; ?>

                        <!-- Footer -->
                        <div class="row mt-5">
                            <div class="col-md-6 text-center">
                                <p>Penyetor,</p>
                                <br><br><br>
                                <p>(.............................)</p>
                            </div>
                            <div class="col-md-6 text-center">
                                <p>Petugas,</p>
                                <br><br><br>
                                <p><u><?php echo e($pembayaran->createdBy->name ?? 'System'); ?></u></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css_inline'); ?>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #printArea,
            #printArea * {
                visibility: visible;
            }

            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .btn,
            .navbar,
            .sidebar {
                display: none !important;
            }
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(0, 0, 0, 0.1);
            font-weight: bold;
            z-index: 1;
        }

        #printArea {
            position: relative;
        }
    </style>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/pembayaran/receipt.blade.php ENDPATH**/ ?>