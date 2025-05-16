<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#" target="_blank">Alluqmaniyyah CMS</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#" target="_blank">CMS</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Starter</li>

            <!-- Menu Home -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_dashboard')): ?>
                <li class="<?php echo e(request()->routeIs('home*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link">
                        <i class="fas fa-home"></i><span>Home</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Menu Data Santri -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_santri')): ?>
                <?php if(Auth::user()->hasRole('admin')): ?>
                    <!-- Jika yang login adalah admin -->
                    <li class="<?php echo e(request()->routeIs('santri*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('santri.index')); ?>" class="nav-link">
                            <i class="fas fa-users"></i><span>Data Santri</span>
                        </a>
                    </li>
                <?php elseif(Auth::user()->hasRole('santri')): ?>
                    <!-- Jika yang login adalah santri -->
                    <li class="<?php echo e(request()->routeIs('santri.show') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('santri.show', Auth::user()->santri->id_santri)); ?>" class="nav-link">
                            <i class="fas fa-users"></i><span>Data Santri</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <li class="menu-header">Roles & Permissions</li>

            <li class="<?php echo e(request()->routeIs('roles.*') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('roles.index')); ?>" class="nav-link">
                    <i class="fas fa-user-cog"></i><span>Roles</span>
                </a>
            </li>



            <li class="<?php echo e(request()->routeIs('permissions.*') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('permissions.index')); ?>" class="nav-link">
                    <i class="fas fa-key"></i><span>Permissions</span>
                </a>
            </li>


            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_user')): ?>
                <li class="menu-header">User</li>
            <?php endif; ?>

            <!-- Menu Data Pengguna -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_user')): ?>
                <li class="<?php echo e(request()->routeIs('user*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('user.index')); ?>" class="nav-link">
                        <i class="fas fa-user-cog"></i><span>Data Pengguna</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="menu-header">Keuangan</li>

            <!-- Menu Biaya -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_biaya_terjadwal', 'view_kategori'])): ?>
                <li class="dropdown <?php echo e(request()->routeIs('biaya_terjadwal*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Biaya</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_biaya_terjadwal')): ?>
                            <li class="<?php echo e(request()->routeIs('biaya_terjadwal*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('biaya_terjadwal.index')); ?>">Biaya Terjadwal</a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_kategori')): ?>
                            <li class="<?php echo e(request()->routeIs('kategori*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('kategori.index')); ?>">Biaya Bulanan</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Tambahan Bulanan -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_tambahan_bulanan', 'view_item_santri'])): ?>
                <li class="dropdown <?php echo e(request()->routeIs('tambahan_bulanan*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Tambahan Bulanan</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_tambahan_bulanan')): ?>
                            <li class="<?php echo e(request()->routeIs('tambahan_bulanan.index') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tambahan_bulanan.index')); ?>">Item Tambahan</a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_item_santri')): ?>
                            <li class="<?php echo e(request()->routeIs('tambahan_bulanan.item_santri*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tambahan_bulanan.item_santri')); ?>">Item Tambahan
                                    Santri</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Tagihan -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_tagihan_terjadwal', 'view_tagihan_bulanan'])): ?>
                <li
                    class="dropdown <?php echo e(request()->routeIs('tagihan_bulanan*') || request()->routeIs('tagihan_terjadwal*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Tagihan</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_tagihan_terjadwal')): ?>
                            <li class="<?php echo e(request()->routeIs('tagihan_terjadwal*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tagihan_terjadwal.index')); ?>">Tagihan Terjadwal</a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_tagihan_bulanan')): ?>
                            <li class="<?php echo e(request()->routeIs('tagihan_bulanan*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tagihan_bulanan.index')); ?>">Tagihan Bulanan</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Pembayaran -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_pembayaran', 'view_riwayat_pembayaran'])): ?>
                <li class="dropdown <?php echo e(request()->routeIs('pembayaran*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Pembayaran</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_pembayaran')): ?>
                            <li class="<?php echo e(request()->routeIs('pembayaran.index') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('pembayaran.index')); ?>">Pembayaran Tagihan</a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_riwayat_pembayaran')): ?>
                            <li class="<?php echo e(request()->routeIs('pembayaran.riwayat') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('pembayaran.riwayat')); ?>">Riwayat Pembayaran</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <li class="menu-header">Madrasah Diniyah</li>

            <!-- Menu Kurikulum -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_mapel_kelas', 'view_kelas', 'view_tahun_ajar', 'view_mapel'])): ?>
                <li
                    class="dropdown <?php echo e(request()->routeIs('mapel*') || request()->routeIs('kelas*') || request()->routeIs('mapel_kelas*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Kurikulum</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_mapel_kelas')): ?>
                            <li class="<?php echo e(request()->routeIs('mapel_kelas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('mapel_kelas.index')); ?>">Mapel Kelas</a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_kelas')): ?>
                            <li class="<?php echo e(request()->routeIs('kelas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('kelas.index')); ?>">Kelas</a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_tahun_ajar')): ?>
                            <li class="<?php echo e(request()->routeIs('tahun_ajar*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tahun_ajar.index')); ?>">Tahun Ajar</a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_mapel')): ?>
                            <li class="<?php echo e(request()->routeIs('mapel*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('mapel.index')); ?>">Mata Pelajaran</a>
                            </li>
                        <?php endif; ?>

                        <li class="<?php echo e(request()->routeIs('mapel*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('qori_kelas.index')); ?>">Qori Kelas</a>
                        </li>

                        
                    </ul>
                </li>
            <?php endif; ?>

            
        </ul>
    </aside>
</div>
<?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>