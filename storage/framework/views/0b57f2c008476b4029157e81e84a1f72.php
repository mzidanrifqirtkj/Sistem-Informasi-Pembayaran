<?php $__env->startSection('title_page', 'Tampil Data Santri'); ?>
<?php $__env->startSection('content'); ?>

    <div class="container">
        <div class="row">
            <div class="col-sm-10">
                <div class="form-group">
                    <?php if($santri->foto != null): ?>
                        <img src="<?php echo e(asset('storage/' . $santri->foto)); ?>" alt="Profile Image Santri" class="rounded-circle"
                            width="200" style="position: relative;width: 200px;height: 200px;overflow: hidden;">
                    <?php else: ?>
                        <img alt="Profile Image Santri" src="<?php echo e(asset('assets/img/avatar/avatar-1.png')); ?>"
                            class="rounded-circle" width="200">
                    <?php endif; ?>
                </div>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('santri.edit')): ?>
                <div class="col-sm-2">
                    <div class="form-group">
                        <a href="<?php echo e(route('santri.edit', $santri->id_santri)); ?>" class="btn btn-info"><i class="fas fa-pen"></i>
                            &nbsp;&nbsp;Edit Profil</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="name">Nama Santri</label>
                    <h4><?php echo e($santri->nama_santri); ?></h4>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="birth_place">Tempat Lahir Santri</label>
                    <h4><?php echo e($santri->tempat_lahir); ?></h4>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="birth_date">Tanggal Lahir Santri</label>
                    <h4><?php echo e(\Carbon\Carbon::parse($santri->tanggal_lahir)->isoFormat('D MMMM Y')); ?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="phone">No. HP Santri</label>
                    <h4><?php echo e($santri->no_hp); ?></h4>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="form-group">
                    <label for="address">Alamat Santri</label>
                    <h4><?php echo e($santri->alamat); ?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="school_old">Asal Sekolah Santri</label>
                    <h4><?php echo e($santri->pendidikan_formal); ?></h4>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="form-group">
                    <label for="school_address_old">Asal Pesantren Santri</label>
                    <h4><?php echo e($santri->pendidikan_non_formal); ?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="school_current">Golongan Darah Santri</label>
                    <h4><?php echo e($santri->golongan_darah); ?></h4>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="form-group">
                    <label for="school_address_current">Email</label>
                    <h4><?php echo e(optional($santri->user)->email ?? '-'); ?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="father_name">Nama Ayah</label>
                    <h4><?php echo e($santri->nama_ayah); ?></h4>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="father_job">Pekerjaan Ayah</label>
                    <h4><?php echo e($santri->pekerjaan_ayah); ?></h4>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="parent_phone">No. HP Ayah</label>
                    <h4><?php echo e($santri->no_hp_ayah); ?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="mother_name">Nama Ibu</label>
                    <h4><?php echo e($santri->nama_ibu); ?></h4>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="form-group">
                    <label for="mother_job">Pekerjaan Ibu</label>
                    <h4><?php echo e($santri->pekerjaan_ibu); ?></h4>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm">
                <div class="form-group">
                    <label for="entry_year">Tahun Masuk</label>
                    <h4><?php echo e($santri->entry_year); ?></h4>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="year_out">Tahun Keluar</label>
                    <h4><?php echo e($santri->year_out ?: '-'); ?></h4>
                </div>
            </div>
        </div>
        <div class="form-group">
            <a href="<?php echo e(route('santri.index')); ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
















<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/santri/show.blade.php ENDPATH**/ ?>