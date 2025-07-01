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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.view')): ?>
                <li class="<?php echo e(request()->routeIs('home*') || request()->routeIs('dashboard*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link">
                        <i class="fas fa-home"></i><span>Home</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Menu Data Santri -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('santri.view')): ?>
                <?php if(Auth::user()->hasRole('admin')): ?>
                    <!-- Jika yang login adalah admin -->
                    <li class="<?php echo e(request()->routeIs('santri*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('santri.index')); ?>" class="nav-link">
                            <i class="fas fa-users"></i><span>Data Santri</span>
                        </a>
                    </li>
                <?php elseif(Auth::user()->hasRole('santri') && Auth::user()->santri): ?>
                    <!-- Jika yang login adalah santri -->
                    <li class="<?php echo e(request()->routeIs('santri.show') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('santri.show', Auth::user()->santri->id_santri)); ?>" class="nav-link">
                            <i class="fas fa-user"></i><span>Data Saya</span>
                        </a>
                    </li>
                <?php elseif(Auth::user()->hasRole('ustadz')): ?>
                    <!-- Jika yang login adalah ustadz -->
                    <li class="<?php echo e(request()->routeIs('santri*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('santri.index')); ?>" class="nav-link">
                            <i class="fas fa-users"></i><span>Data Santri</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Roles & Permissions - Only for Admin -->
            <?php if(Auth::user()->hasRole('admin')): ?>
                <li class="menu-header">Roles & Permissions</li>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.view')): ?>
                    <li class="<?php echo e(request()->routeIs('roles.*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('roles.index')); ?>" class="nav-link">
                            <i class="fas fa-user-cog"></i><span>Roles</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permissions.view')): ?>
                    <li class="<?php echo e(request()->routeIs('permissions.*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('permissions.index')); ?>" class="nav-link">
                            <i class="fas fa-key"></i><span>Permissions</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Menu Data Pengguna - Only for Admin -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user.view')): ?>
                <li class="menu-header">User Management</li>
                <li class="<?php echo e(request()->routeIs('user*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('user.index')); ?>" class="nav-link">
                        <i class="fas fa-user-cog"></i><span>Data Pengguna</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="menu-header">Keuangan</li>

            <!-- Menu Biaya -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['biaya-santri.view', 'daftar-biaya.view', 'kategori-biaya.view'])): ?>
                <li
                    class="dropdown <?php echo e(request()->routeIs('biaya-santris*') || request()->routeIs('daftar-biayas*') || request()->routeIs('kategori-biayas*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-money-bill-alt"></i> <span>Master Biaya</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('biaya-santri.view')): ?>
                            <li class="<?php echo e(request()->routeIs('biaya-santris*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('biaya-santris.index')); ?>">
                                    <i class="fas fa-user-graduate"></i> Biaya Santri
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('daftar-biaya.view')): ?>
                            <li class="<?php echo e(request()->routeIs('daftar-biayas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('daftar-biayas.index')); ?>">
                                    <i class="fas fa-list-ul"></i> Daftar Biaya
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('kategori-biaya.view')): ?>
                            <li class="<?php echo e(request()->routeIs('kategori-biayas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('kategori-biayas.index')); ?>">
                                    <i class="fas fa-tags"></i> Kategori Biaya
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Tagihan -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['tagihan-terjadwal.view', 'tagihan-bulanan.view', 'tambahan-bulanan.view'])): ?>
                <li
                    class="dropdown <?php echo e(request()->routeIs('tagihan_bulanan*') || request()->routeIs('tagihan_terjadwal*') || request()->routeIs('tambahan_bulanan*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice-dollar"></i> <span>Tagihan</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tagihan-terjadwal.view')): ?>
                            <li class="<?php echo e(request()->routeIs('tagihan_terjadwal*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tagihan_terjadwal.index')); ?>">
                                    <i class="fas fa-calendar-check"></i> Tagihan Terjadwal
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tagihan-bulanan.view')): ?>
                            <li class="<?php echo e(request()->routeIs('tagihan_bulanan*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tagihan_bulanan.index')); ?>">
                                    <i class="fas fa-calendar-alt"></i> Tagihan Bulanan
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tambahan-bulanan.view')): ?>
                            <li class="<?php echo e(request()->routeIs('tambahan_bulanan*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tambahan_bulanan.index')); ?>">
                                    <i class="fas fa-plus-circle"></i> Tambahan Bulanan
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Pembayaran -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['pembayaran.list', 'pembayaran.create', 'pembayaran.view', 'pembayaran.history'])): ?>
                <li class="dropdown <?php echo e(request()->routeIs('pembayaran*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-money-check"></i> <span>Pembayaran</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['pembayaran.list', 'pembayaran.create'])): ?>
                            <li
                                class="<?php echo e(request()->routeIs('pembayaran.index') || request()->routeIs('pembayaran.show') ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('pembayaran.index')); ?>" class="nav-link">
                                    <i class="fas fa-money-bill-wave"></i> Pembayaran
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pembayaran.history')): ?>
                            <li class="<?php echo e(request()->routeIs('pembayaran.history') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('pembayaran.history')); ?>">
                                    <i class="fas fa-history"></i> Riwayat Pembayaran
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->hasRole('admin')): ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pembayaran.bulk')): ?>
                                <li class="<?php echo e(request()->routeIs('pembayaran.bulk*') ? 'active' : ''); ?>">
                                    <a class="nav-link" href="<?php echo e(route('pembayaran.bulk.index')); ?>">
                                        <i class="fas fa-layer-group"></i> Pembayaran Bulk
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <li class="menu-header">Madrasah Diniyah</li>

            <!-- Menu Kurikulum -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['kelas.view', 'mapel.view', 'mapel-kelas.view', 'tahun-ajar.view', 'qori-kelas.view',
                'riwayat-kelas.view'])): ?>
                <li
                    class="dropdown <?php echo e(request()->routeIs('mapel*') || request()->routeIs('kelas*') || request()->routeIs('mapel_kelas*') || request()->routeIs('tahun_ajar*') || request()->routeIs('qori_kelas*') || request()->routeIs('riwayat-kelas*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-graduation-cap"></i> <span>Kurikulum</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('riwayat-kelas.view')): ?>
                            <li class="<?php echo e(request()->routeIs('riwayat-kelas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('riwayat-kelas.index')); ?>">
                                    <i class="fas fa-clock"></i> Riwayat Kelas
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('mapel-kelas.view')): ?>
                            <li class="<?php echo e(request()->routeIs('mapel_kelas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('mapel_kelas.index')); ?>">
                                    <i class="fas fa-chalkboard-teacher"></i> Mapel Kelas
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('kelas.view')): ?>
                            <li class="<?php echo e(request()->routeIs('kelas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('kelas.index')); ?>">
                                    <i class="fas fa-door-open"></i> Kelas
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tahun-ajar.view')): ?>
                            <li class="<?php echo e(request()->routeIs('tahun_ajar*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('tahun_ajar.index')); ?>">
                                    <i class="fas fa-calendar"></i> Tahun Ajar
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('mapel.view')): ?>
                            <li
                                class="<?php echo e(request()->routeIs('mapel.index') && !request()->routeIs('mapel_kelas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('mapel.index')); ?>">
                                    <i class="fas fa-book-open"></i> Mata Pelajaran
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('qori-kelas.view')): ?>
                            <li class="<?php echo e(request()->routeIs('qori_kelas*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('qori_kelas.index')); ?>">
                                    <i class="fas fa-quran"></i> Qori Kelas
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Ustadz -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['ustadz.view', 'penugasan-ustadz.view'])): ?>
                <li class="dropdown <?php echo e(request()->routeIs('ustadz*') ? 'active' : ''); ?>">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-user-tie"></i> <span>Ustadz</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ustadz.view')): ?>
                            <li class="<?php echo e(request()->routeIs('ustadz.get') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('ustadz.get')); ?>">
                                    <i class="fas fa-users"></i> Data Ustadz
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('penugasan-ustadz.view')): ?>
                            <li class="<?php echo e(request()->routeIs('ustadz.penugasan*') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('ustadz.penugasan.index')); ?>">
                                    <i class="fas fa-tasks"></i> Penugasan Ustadz
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Absensi - Commented out as requested in original -->
            

            <!-- Profile Menu - Available for all logged users -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('profile.view')): ?>
                <li class="menu-header">Profile</li>
                <li class="<?php echo e(request()->routeIs('profile*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('profile.edit')); ?>" class="nav-link">
                        <i class="fas fa-user-circle"></i><span>Profile</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </aside>
</div>
<?php /**PATH C:\laragon\www\Zidan\Akprind\Sistem-Informasi-Pembayaran-PPLQ\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>