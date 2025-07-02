<?php $__env->startSection('title_page', 'Riwayat Pembayaran'); ?>

<?php $__env->startSection('css_inline'); ?>
    <style>
        /* Fix z-index conflict between SweetAlert2 and Bootstrap Modal */
        .swal2-container {
            z-index: 10000 !important;
            /* Higher than Bootstrap modal (1050) */
        }

        /* Ensure modal backdrop doesn't interfere */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        /* Fix untuk multiple backdrop */
        .modal-backdrop+.modal-backdrop {
            opacity: 0;
            display: none;
        }

        .modal-backdrop.show {
            opacity: 0 !important;
            /* Transparan sepenuhnya */
            pointer-events: none !important;
            background-color: transparent !important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php
        $user = auth()->user();
    ?>
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-8">
                <h1>Riwayat Pembayaran</h1>
            </div>
            <div class="col-md-4 text-right">
                <a href="<?php echo e(route('pembayaran.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form action="<?php echo e(route('pembayaran.history')); ?>" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua</option>
                            <option value="0" <?php echo e(request('status') === '0' ? 'selected' : ''); ?>>Aktif</option>
                            <option value="1" <?php echo e(request('status') === '1' ? 'selected' : ''); ?>>Void</option>
                        </select>
                    </div>
                    <?php if (! (auth()->user()->hasRole('santri'))): ?>
                        <div class="col-md-2">
                            <label>Cari</label>
                            <input type="text" name="search" class="form-control" placeholder="No/Nama/NIS"
                                value="<?php echo e(request('search')); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="<?php echo e(route('pembayaran.history')); ?>" class="btn btn-secondary">
                                <i class="fas fa-sync"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Hari Ini</h6>
                        <h4>Rp
                            <?php echo e(number_format($user->hasRole('santri') ? \App\Models\Pembayaran::getTodayTotalForSantri($user->santri->id_santri) : \App\Models\Pembayaran::getTodayTotal(), 0, ',', '.')); ?>

                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Total Bulan Ini</h6>
                        <h4>Rp
                            <?php echo e(number_format($user->hasRole('santri') ? \App\Models\Pembayaran::getMonthTotalForSantri($user->santri->id_santri) : \App\Models\Pembayaran::getMonthTotal(), 0, ',', '.')); ?>

                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Total Tahun Ini</h6>
                        <h4>Rp
                            <?php echo e(number_format($user->hasRole('santri') ? \App\Models\Pembayaran::getYearTotalForSantri($user->santri->id_santri) : \App\Models\Pembayaran::getYearTotal(), 0, ',', '.')); ?>

                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Transaksi Hari Ini</h6>
                        <h4><?php echo e(\App\Models\Pembayaran::whereDate('tanggal_pembayaran', today())->count()); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">No. Kwitansi</th>
                                <th width="10%">Tanggal</th>
                                <th width="10%">NIS</th>
                                <th>Nama Santri</th>
                                <th>Deskripsi</th>
                                <th width="15%">Nominal</th>
                                <th width="8%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $pembayarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pembayaran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="<?php echo e($pembayaran->is_void ? 'table-danger' : ''); ?>">
                                    <td><?php echo e($pembayarans->firstItem() + $index); ?></td>
                                    <td>
                                        <strong><?php echo e($pembayaran->receipt_number); ?></strong>
                                    </td>
                                    <td><?php echo e($pembayaran->tanggal_pembayaran->format('d/m/Y')); ?></td>
                                    <td><?php echo e($pembayaran->santri_nis); ?></td>
                                    <td><?php echo e($pembayaran->santri_name); ?></td>
                                    <td>
                                        <?php echo e($pembayaran->payment_description); ?>

                                        <?php if($pembayaran->payment_note): ?>
                                            <br><small class="text-muted"><?php echo e($pembayaran->payment_note); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <strong><?php echo e($pembayaran->formatted_nominal); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $pembayaran->status_badge; ?>

                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('pembayaran.receipt', $pembayaran->id_pembayaran)); ?>"
                                            class="btn btn-sm btn-info" target="_blank" title="Lihat Kwitansi">
                                            <i class="fas fa-print"></i>
                                        </a>

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pembayaran-void')): ?>
                                            <?php if($pembayaran->can_void): ?>
                                                <button onclick="showVoidModal(<?php echo e($pembayaran->id_pembayaran); ?>)"
                                                    class="btn btn-sm btn-danger" title="Void Pembayaran">
                                                    <i class="fas fa-ban"></i> Void
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if($pembayaran->is_void): ?>
                                            <button class="btn btn-sm btn-secondary"
                                                onclick="showVoidInfo(<?php echo e($pembayaran->id_pembayaran); ?>)" title="Info Void">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data pembayaran</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan <?php echo e($pembayarans->firstItem() ?? 0); ?> - <?php echo e($pembayarans->lastItem() ?? 0); ?>

                        dari <?php echo e($pembayarans->total()); ?> pembayaran
                    </div>
                    <div>
                        <?php echo e($pembayarans->links('pagination::bootstrap-5')); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Void Modal -->
    <div class="modal fade" id="voidModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="voidForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Void Pembayaran</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin void pembayaran ini?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Pembayaran yang di-void tidak dapat dikembalikan.
                        </div>
                        <div class="form-group">
                            <label>Alasan Void <span class="text-danger">*</span></label>
                            <textarea name="void_reason" class="form-control" rows="3" placeholder="Masukkan alasan void..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban"></i> Void Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Void Info Modal -->
    <div class="modal fade" id="voidInfoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informasi Void</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="voidInfoContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        function showVoidModal(id) {
            const form = document.getElementById('voidForm');
            form.action = `<?php echo e(url('pembayaran/void')); ?>/${id}`;
            form.querySelector('textarea[name="void_reason"]').value = '';
            $('#voidModal').modal('show');
        }

        function showVoidInfo(id) {
            // Get void info via AJAX
            $.get(`<?php echo e(url('pembayaran/void')); ?>/${id}/info`, function(data) {
                let content = `
            <table class="table table-sm">
                <tr>
                    <td width="40%">Di-void oleh</td>
                    <td>: ${data.voided_by_name}</td>
                </tr>
                <tr>
                    <td>Tanggal Void</td>
                    <td>: ${data.voided_at}</td>
                </tr>
                <tr>
                    <td>Alasan</td>
                    <td>: ${data.void_reason}</td>
                </tr>
            </table>
        `;
                $('#voidInfoContent').html(content);
                $('#voidInfoModal').modal('show');
            }).fail(function() {
                alert('Gagal memuat informasi void');
            });
        }

        // Auto submit filter on date change
        $('input[type="date"]').on('change', function() {
            if ($('input[name="start_date"]').val() && $('input[name="end_date"]').val()) {
                $(this).closest('form').submit();
            }
        });

        // Handle void form submission with AJAX
        $('#voidForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Disable button dan show loading
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#voidModal').modal('hide');

                    // Show success message
                    if (response.success) {
                        // Reload page untuk refresh data
                        location.reload();
                    }
                },
                error: function(xhr) {
                    let message = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                },
                complete: function() {
                    // Re-enable button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/pembayaran/history.blade.php ENDPATH**/ ?>