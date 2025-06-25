<!-- resources/views/biaya_santri/create.blade.php -->

<?php $__env->startSection('title_page', 'Tambah Biaya Santri'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Biaya Santri</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('biaya-santris.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="santri_id">Pilih Santri</label>
                    <select class="form-control" name="santri_id" id="santri_id" required>
                        <option value="">-- Pilih Santri --</option>
                        <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($santri->id_santri); ?>"><?php echo e($santri->nama_santri); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>


                <div class="form-group">
                    <label>Pilih Biaya</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="biaya_search" placeholder="Cari biaya...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="search_biaya_btn">Cari</button>
                        </div>
                    </div>
                    <div id="biaya_results" class="mb-3"></div>

                    <div class="selected-biaya">
                        <h6>Biaya Terpilih:</h6>
                        <div id="selected_biaya_list"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?php echo e(route('biaya-santris.index')); ?>" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            // Santri search
            $('#santri_search').on('input', function() {
                let query = $(this).val();
                if (query.length > 2) {
                    $.get("<?php echo e(route('biaya-santris.search-santri')); ?>", {
                        q: query
                    }, function(data) {
                        let html = '<ul class="list-group">';
                        data.forEach(santri => {
                            html +=
                                `<li class="list-group-item santri-item" data-id="${santri.id_santri}">${santri.nama_santri}</li>`;
                        });
                        html += '</ul>';
                        $('#santri_result').html(html);
                    });
                } else {
                    $('#santri_result').empty();
                }
            });

            $(document).on('click', '.santri-item', function() {
                let id = $(this).data('id');
                let name = $(this).text();
                $('#santri_id').val(id);
                $('#santri_search').val(name);
                $('#santri_result').empty();
            });

            // Biaya search
            $('#search_biaya_btn').click(function() {
                let query = $('#biaya_search').val();
                if (query.length > 0) {
                    $.get("<?php echo e(route('biaya-santris.search-biaya')); ?>", {
                        q: query
                    }, function(data) {
                        let html = '<div class="row">';
                        data.forEach(biaya => {
                            html += `
                                <div class="col-md-4 mb-3">
                                    <div class="card biaya-item" data-id="${biaya.id_daftar_biaya}">
                                        <div class="card-body">
                                            <h5 class="card-title">${biaya.kategori_biaya.nama_kategori}</h5>
                                            <p class="card-text">Rp ${biaya.nominal.toLocaleString()}</p>
                                            <button type="button" class="btn btn-sm btn-primary add-biaya">Tambah</button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        $('#biaya_results').html(html);
                    });
                }
            });

            // Add biaya to selected list
            $(document).on('click', '.add-biaya', function() {
                let card = $(this).closest('.biaya-item');
                let id = card.data('id');
                let title = card.find('.card-title').text();
                let nominal = card.find('.card-text').text();

                // Check if already selected
                if ($(`#selected_biaya_${id}`).length) {
                    return;
                }

                let html = `
                    <div class="card mb-2" id="selected_biaya_${id}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>${title}</h6>
                                    <p>${nominal}</p>
                                    <input type="hidden" name="biaya[${id}][id]" value="${id}">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="biaya[${id}][jumlah]" class="form-control" value="1" min="1">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-biaya" data-id="${id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#selected_biaya_list').append(html);
            });

            // Remove selected biaya
            $(document).on('click', '.remove-biaya', function() {
                let id = $(this).data('id');
                $(`#selected_biaya_${id}`).remove();
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/biaya-santris/create.blade.php ENDPATH**/ ?>