<?php $__env->startSection('title_page', 'Edit Tagihan Terjadwal'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Edit Tagihan Terjadwal</h2>
                    <a href="<?php echo e(route('tagihan_terjadwal.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Current Data Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle text-info"></i> Informasi Tagihan Saat Ini
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Santri:</small><br>
                                <strong><?php echo e($tagihanTerjadwal->santri->nama_santri ?? 'N/A'); ?></strong>
                                <span class="badge badge-info ml-2"><?php echo e($tagihanTerjadwal->santri->nis ?? 'N/A'); ?></span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Jenis Biaya:</small><br>
                                <strong><?php echo e($tagihanTerjadwal->daftarBiaya->kategoriBiaya->nama_kategori ?? 'N/A'); ?></strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Status Saat Ini:</small><br>
                                <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($tagihanTerjadwal->status) {
                                        case 'belum_lunas':
                                            $statusClass = 'badge-danger';
                                            $statusText = 'Belum Lunas';
                                            break;
                                        case 'dibayar_sebagian':
                                            $statusClass = 'badge-warning';
                                            $statusText = 'Dibayar Sebagian';
                                            break;
                                        case 'lunas':
                                            $statusClass = 'badge-success';
                                            $statusText = 'Lunas';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                            $statusText = ucfirst($tagihanTerjadwal->status);
                                    }
                                ?>
                                <span class="badge <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit"></i> Form Edit Tagihan
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('tagihan_terjadwal.update', $tagihanTerjadwal->id_tagihan_terjadwal)); ?>"
                            method="POST" id="editTagihanForm">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <div class="row">
                                <!-- Santri Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="santri_id" class="required">Pilih Santri</label>
                                        <select name="santri_id" id="santri_id"
                                            class="form-control <?php $__errorArgs = ['santri_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="">-- Pilih Santri --</option>
                                            <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($santri->id_santri); ?>"
                                                    <?php echo e(old('santri_id', $tagihanTerjadwal->santri_id) == $santri->id_santri ? 'selected' : ''); ?>>
                                                    <?php echo e($santri->nama_santri); ?> (<?php echo e($santri->nis); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['santri_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <!-- Tahun -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tahun" class="required">Tahun</label>
                                        <select name="tahun" id="tahun"
                                            class="form-control <?php $__errorArgs = ['tahun'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="">-- Pilih Tahun --</option>
                                            <?php for($year = now()->year - 2; $year <= now()->year + 2; $year++): ?>
                                                <option value="<?php echo e($year); ?>"
                                                    <?php echo e(old('tahun', $tagihanTerjadwal->tahun) == $year ? 'selected' : ''); ?>>
                                                    <?php echo e($year); ?>

                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        <?php $__errorArgs = ['tahun'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Biaya Santri Selection -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="biaya_santri_id" class="required">Pilih Alokasi Biaya</label>
                                        <select name="biaya_santri_id" id="biaya_santri_id"
                                            class="form-control <?php $__errorArgs = ['biaya_santri_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="">-- Memuat data... --</option>
                                            <?php $__currentLoopData = $biayaSantrisUntukSantri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biayaSantri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($biayaSantri['id']); ?>"
                                                    data-daftar-biaya-id="<?php echo e($biayaSantri['daftar_biaya_id']); ?>"
                                                    data-nominal="<?php echo e($biayaSantri['nominal_tagihan_default']); ?>"
                                                    <?php echo e(old('biaya_santri_id', $tagihanTerjadwal->biaya_santri_id) == $biayaSantri['id'] ? 'selected' : ''); ?>>
                                                    <?php echo e($biayaSantri['nama_biaya_paket']); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle text-info"></i>
                                            Hanya menampilkan alokasi biaya dengan kategori <strong>Tahunan</strong> dan
                                            <strong>Insidental</strong>.
                                        </small>
                                        <?php $__errorArgs = ['biaya_santri_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden field for daftar_biaya_id -->
                            <input type="hidden" name="daftar_biaya_id" id="daftar_biaya_id"
                                value="<?php echo e(old('daftar_biaya_id', $tagihanTerjadwal->daftar_biaya_id)); ?>">

                            <div class="row">
                                <!-- Tahun Ajar -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tahun_ajar_id">Tahun Ajar (Opsional)</label>
                                        <select name="tahun_ajar_id" id="tahun_ajar_id"
                                            class="form-control <?php $__errorArgs = ['tahun_ajar_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <option value="">-- Tidak Ada --</option>
                                            <?php $__currentLoopData = $tahunAjars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahunAjar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($tahunAjar->id_tahun_ajar); ?>"
                                                    <?php echo e(old('tahun_ajar_id', $tagihanTerjadwal->tahun_ajar_id) == $tahunAjar->id_tahun_ajar ? 'selected' : ''); ?>>
                                                    <?php echo e($tahunAjar->tahun_ajar); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['tahun_ajar_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <!-- Nominal -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nominal" class="required">Nominal Tagihan</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number" name="nominal" id="nominal"
                                                class="form-control <?php $__errorArgs = ['nominal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                placeholder="0" min="0" step="1000" required
                                                value="<?php echo e(old('nominal', $tagihanTerjadwal->nominal)); ?>">
                                        </div>
                                        <small class="form-text text-muted">
                                            Nominal dapat disesuaikan dari nominal default alokasi biaya.
                                        </small>
                                        <?php $__errorArgs = ['nominal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Status -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="status" class="required">Status Tagihan</label>
                                        <select name="status" id="status"
                                            class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="belum_lunas"
                                                <?php echo e(old('status', $tagihanTerjadwal->status) == 'belum_lunas' ? 'selected' : ''); ?>>
                                                Belum Lunas
                                            </option>
                                            <option value="dibayar_sebagian"
                                                <?php echo e(old('status', $tagihanTerjadwal->status) == 'dibayar_sebagian' ? 'selected' : ''); ?>>
                                                Dibayar Sebagian
                                            </option>
                                            <option value="lunas"
                                                <?php echo e(old('status', $tagihanTerjadwal->status) == 'lunas' ? 'selected' : ''); ?>>
                                                Lunas
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle text-info"></i>
                                            Status ini akan otomatis diperbarui berdasarkan pembayaran yang masuk.
                                        </small>
                                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-group mt-4">
                                <div class="d-flex justify-content-between">
                                    <button type="button" onclick="window.history.back()" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save"></i> Update Tagihan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 mb-0">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            // Handle santri selection change
            $('#santri_id').on('change', function() {
                const santriId = $(this).val();
                const biayaSantriSelect = $('#biaya_santri_id');

                if (santriId) {
                    // Show loading
                    $('#loadingModal').modal('show');
                    biayaSantriSelect.prop('disabled', true).html('<option value="">Memuat...</option>');

                    // Fetch biaya santri data
                    $.ajax({
                        url: '<?php echo e(route('tagihan_terjadwal.getBiayaSantriBySantriId')); ?>',
                        type: 'GET',
                        data: {
                            santri_id: santriId
                        },
                        success: function(data) {
                            biayaSantriSelect.empty().append(
                                '<option value="">-- Pilih Alokasi Biaya --</option>');

                            if (data.length > 0) {
                                $.each(data, function(index, item) {
                                    const isSelected = item.id ==
                                        '<?php echo e(old('biaya_santri_id', $tagihanTerjadwal->biaya_santri_id)); ?>';
                                    biayaSantriSelect.append(
                                        '<option value="' + item.id +
                                        '" data-daftar-biaya-id="' + item
                                        .daftar_biaya_id + '" data-nominal="' + item
                                        .nominal_tagihan_default + '"' + (
                                            isSelected ? ' selected' : '') + '>' +
                                        item.nama_biaya_paket +
                                        '</option>'
                                    );
                                });
                                biayaSantriSelect.prop('disabled', false);

                                // Trigger change to set daftar_biaya_id
                                biayaSantriSelect.trigger('change');
                            } else {
                                biayaSantriSelect.append(
                                    '<option value="">Tidak ada alokasi biaya Tahunan/Insidental untuk santri ini</option>'
                                );
                            }
                        },
                        error: function(xhr) {
                            console.error('Error fetching biaya santri:', xhr);
                            biayaSantriSelect.empty().append(
                                '<option value="">Error memuat data</option>');

                            // Show error message
                            alert(
                                'Terjadi kesalahan saat memuat data alokasi biaya. Silakan coba lagi.'
                                );
                        },
                        complete: function() {
                            $('#loadingModal').modal('hide');
                        }
                    });
                } else {
                    biayaSantriSelect.prop('disabled', true).html(
                        '<option value="">-- Pilih Santri Terlebih Dahulu --</option>');
                    $('#daftar_biaya_id').val('');
                }
            });

            // Handle biaya santri selection change
            $('#biaya_santri_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const daftarBiayaId = selectedOption.data('daftar-biaya-id');

                if (daftarBiayaId) {
                    $('#daftar_biaya_id').val(daftarBiayaId);
                } else {
                    $('#daftar_biaya_id').val('');
                }
            });

            // Format number input
            $('#nominal').on('input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(value);
            });

            // Form validation before submit
            $('#editTagihanForm').on('submit', function(e) {
                const santriId = $('#santri_id').val();
                const biayaSantriId = $('#biaya_santri_id').val();
                const nominal = $('#nominal').val();
                const tahun = $('#tahun').val();

                if (!santriId || !biayaSantriId || !nominal || !tahun) {
                    e.preventDefault();
                    alert('Mohon lengkapi semua field yang wajib diisi.');
                    return false;
                }

                if (parseFloat(nominal) < 0) {
                    e.preventDefault();
                    alert('Nominal tagihan tidak boleh negatif.');
                    return false;
                }

                // Confirm if user really wants to update
                if (!confirm('Apakah Anda yakin ingin mengupdate tagihan ini?')) {
                    e.preventDefault();
                    return false;
                }

                // Disable submit button to prevent double submission
                $('#submitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            });

            // Initialize form if santri is already selected
            if ($('#santri_id').val()) {
                // Set initial daftar_biaya_id from selected biaya_santri
                const selectedBiayaSantri = $('#biaya_santri_id').find('option:selected');
                const daftarBiayaId = selectedBiayaSantri.data('daftar-biaya-id');
                if (daftarBiayaId) {
                    $('#daftar_biaya_id').val(daftarBiayaId);
                }
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css_inline'); ?>
    <style>
        .required::after {
            content: ' *';
            color: red;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
        }

        .input-group-text {
            background-color: #e9ecef;
            border-color: #ced4da;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        .invalid-feedback {
            display: block;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .badge {
            font-size: 0.8rem;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/tagihan-terjadwal/edit.blade.php ENDPATH**/ ?>