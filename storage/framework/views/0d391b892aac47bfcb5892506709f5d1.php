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
                        <i class="fas fa-money-bill-alt"></i> <span>List Biaya</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="<?php echo e(request()->routeIs('biaya-santris*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('biaya-santris.index')); ?>">
                                <i class="fas fa-user-graduate"></i> Biaya Santri
                            </a>
                        </li>

                        <li class="<?php echo e(request()->routeIs('daftar-biayas*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('daftar-biayas.index')); ?>">
                                <i class="fas fa-list-ul"></i> Daftar Biaya
                            </a>
                        </li>

                        <li class="<?php echo e(request()->routeIs('kategori-biayas*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('kategori-biayas.index')); ?>">
                                <i class="fas fa-tags"></i> Kategori Biaya
                            </a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Tagihan -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_tagihan_terjadwal', 'view_tagihan_bulanan'])): ?>
                <li
                    class="dropdown <?php echo e(request()->routeIs('tagihan_bulanan*') || request()->routeIs('tagihan_terjadwal*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice-dollar"></i> <span>Tagihan</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_tagihan_terjadwal')): ?>
                            <li class="<?php echo e(request()->routeIs('tagihan_terjadwal*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tagihan_terjadwal.index')); ?>">
                                    <i class="fas fa-calendar-check"></i> Tagihan Terjadwal
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_tagihan_bulanan')): ?>
                            <li class="<?php echo e(request()->routeIs('tagihan_bulanan*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tagihan_bulanan.index')); ?>">
                                    <i class="fas fa-calendar-alt"></i> Tagihan Bulanan
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Pembayaran -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_pembayaran', 'view_riwayat_pembayaran'])): ?>
                <li class="dropdown <?php echo e(request()->routeIs('pembayaran*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-money-check"></i> <span>Pembayaran</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['pembayaran-list', 'pembayaran-create'])): ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route('pembayaran.index')); ?>" class="nav-link">
                                    <i class="fas fa-money-bill-wave"></i> Pembayaran
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_riwayat_pembayaran')): ?>
                            <li class="<?php echo e(request()->routeIs('pembayaran.history') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('pembayaran.history')); ?>">
                                    <i class="fas fa-history"></i> Riwayat
                                </a>
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
                        <i class="fas fa-graduation-cap"></i> <span>Kurikulum</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="<?php echo e(request()->routeIs('riwayat-kelas*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('riwayat-kelas.index')); ?>">
                                <i class="fas fa-clock"></i> Riwayat Kelas
                            </a>
                        </li>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_mapel_kelas')): ?>
                            <li class="<?php echo e(request()->routeIs('mapel_kelas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('mapel_kelas.index')); ?>">
                                    <i class="fas fa-chalkboard-teacher"></i> Mapel Kelas
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_kelas')): ?>
                            <li class="<?php echo e(request()->routeIs('kelas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('kelas.index')); ?>">
                                    <i class="fas fa-door-open"></i> Kelas
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_tahun_ajar')): ?>
                            <li class="<?php echo e(request()->routeIs('tahun_ajar*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tahun_ajar.index')); ?>">
                                    <i class="fas fa-calendar"></i> Tahun Ajar
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_mapel')): ?>
                            <li class="<?php echo e(request()->routeIs('mapel*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('mapel.index')); ?>">
                                    <i class="fas fa-book-open"></i> Mata Pelajaran
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="<?php echo e(request()->routeIs('qori_kelas*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('qori_kelas.index')); ?>">
                                <i class="fas fa-quran"></i> Qori Kelas
                            </a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>

            
        </ul>
    </aside>
</div>
<?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>