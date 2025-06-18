<?php $__env->startSection('title_page', 'Buat Tagihan Bulanan Individual'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Form Buat Tagihan Bulanan</h4>
                    </div>
                    <div class="card-body">
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('tagihan_bulanan.store')); ?>" id="createForm">
                            <?php echo csrf_field(); ?>

                            <div class="mb-3">
                                <label for="santri_id" class="form-label required">Pilih Santri</label>
                                <select name="santri_id" id="santri_id" class="form-select" required>
                                    <option value="">-- Pilih Santri --</option>
                                    <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($santri->id_santri); ?>"
                                            <?php echo e(old('santri_id') == $santri->id_santri ? 'selected' : ''); ?>>
                                            <?php echo e($santri->nama_santri); ?> (<?php echo e($santri->nis); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bulan" class="form-label required">Bulan</label>
                                    <select name="bulan" id="bulan" class="form-select" required>
                                        <option value="">-- Pilih Bulan --</option>
                                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"
                                                <?php echo e(old('bulan') == $key ? 'selected' : ''); ?>>
                                                <?php echo e($key); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tahun" class="form-label required">Tahun</label>
                                    <select name="tahun" id="tahun" class="form-select" required>
                                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($year); ?>"
                                                <?php echo e(old('tahun', date('Y')) == $year ? 'selected' : ''); ?>>
                                                <?php echo e($year); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div id="biayaInfo" style="display: none;">
                                <hr>
                                <h5>Rincian Biaya</h5>
                                <div id="rincianList"></div>
                                <div class="alert alert-info mt-3">
                                    <strong>Total: <span id="totalNominal">Rp 0</span></strong>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nominal" class="form-label">Nominal Custom (Opsional)</label>
                                <input type="number" name="nominal" id="nominal" class="form-control"
                                    value="<?php echo e(old('nominal')); ?>" min="0"
                                    placeholder="Kosongkan untuk menggunakan total dari rincian">
                                <small class="form-text text-muted">
                                    Isi jika ingin menggunakan nominal berbeda dari total rincian
                                </small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="useCustomRincian">
                                    <label class="form-check-label" for="useCustomRincian">
                                        Gunakan rincian custom
                                    </label>
                                </div>
                            </div>

                            <div id="customRincianSection" style="display: none;">
                                <h5>Rincian Custom</h5>
                                <div id="customRincianList"></div>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="addCustomRincian()">
                                    <i class="fas fa-plus"></i> Tambah Rincian
                                </button>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <a href="<?php echo e(route('tagihan_bulanan.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Tagihan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            $('#santri_id').on('change', function() {
                const santriId = $(this).val();
                if (santriId) {
                    loadSantriBiaya(santriId);
                } else {
                    $('#biayaInfo').hide();
                }
            });

            $('#useCustomRincian').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#customRincianSection').show();
                    $('#biayaInfo').hide();
                } else {
                    $('#customRincianSection').hide();
                    if ($('#santri_id').val()) {
                        $('#biayaInfo').show();
                    }
                }
            });

            // Load initial data if santri selected
            if ($('#santri_id').val()) {
                loadSantriBiaya($('#santri_id').val());
            }
        });

        function loadSantriBiaya(santriId) {
            $.ajax({
                url: "<?php echo e(route('tagihan_bulanan.getSantriBiayaInfo')); ?>",
                method: 'GET',
                data: {
                    santri_id: santriId
                },
                beforeSend: function() {
                    $('#rincianList').html(
                        '<div class="text-center"><span class="spinner-border spinner-border-sm"></span> Loading...</div>'
                    );
                },
                success: function(response) {
                    if (response.success) {
                        let html = '<table class="table table-sm">';
                        html += '<thead><tr><th>Jenis Biaya</th><th class="text-end">Nominal</th></tr></thead>';
                        html += '<tbody>';

                        response.rincian.forEach(function(item) {
                            html += `<tr>
                        <td>${item.nama}</td>
                        <td class="text-end">Rp ${number_format(item.nominal)}</td>
                    </tr>`;
                        });

                        html += '</tbody></table>';

                        $('#rincianList').html(html);
                        $('#totalNominal').text(response.formatted_total);
                        $('#biayaInfo').show();
                    } else {
                        $('#rincianList').html(
                            '<div class="alert alert-warning">Santri tidak memiliki alokasi biaya kategori tambahan/jalur</div>'
                        );
                        $('#biayaInfo').show();
                    }
                },
                error: function() {
                    $('#rincianList').html('<div class="alert alert-danger">Error loading data</div>');
                    $('#biayaInfo').show();
                }
            });
        }

        let customRincianIndex = 0;

        function addCustomRincian() {
            const html = `
        <div class="row mb-2" id="rincian_${customRincianIndex}">
            <div class="col-md-6">
                <input type="text"
                       name="custom_rincian[${customRincianIndex}][nama]"
                       class="form-control form-control-sm"
                       placeholder="Nama biaya"
                       required>
            </div>
            <div class="col-md-4">
                <input type="number"
                       name="custom_rincian[${customRincianIndex}][nominal]"
                       class="form-control form-control-sm"
                       placeholder="Nominal"
                       min="0"
                       required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeCustomRincian(${customRincianIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
            $('#customRincianList').append(html);
            customRincianIndex++;
        }

        function removeCustomRincian(index) {
            $(`#rincian_${index}`).remove();
        }

        function number_format(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/tagihan_bulanan/create.blade.php ENDPATH**/ ?>