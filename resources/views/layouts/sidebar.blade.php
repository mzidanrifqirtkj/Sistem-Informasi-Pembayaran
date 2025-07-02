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
            @can('dashboard.view')
                <li class="{{ request()->routeIs('home*') || request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i><span>Home</span>
                    </a>
                </li>
            @endcan

            <!-- Menu Data Santri -->
            @can('santri.view')
                @if (Auth::user()->hasRole('admin'))
                    <!-- Jika yang login adalah admin -->
                    <li class="{{ request()->routeIs('santri*') ? 'active' : '' }}">
                        <a href="{{ route('santri.index') }}" class="nav-link">
                            <i class="fas fa-users"></i><span>Data Santri</span>
                        </a>
                    </li>
                @elseif (Auth::user()->hasRole('santri') && Auth::user()->santri)
                    <!-- Jika yang login adalah santri -->
                    <li class="{{ request()->routeIs('santri.show') ? 'active' : '' }}">
                        <a href="{{ route('santri.show', Auth::user()->santri->id_santri) }}" class="nav-link">
                            <i class="fas fa-user"></i><span>Data Saya</span>
                        </a>
                    </li>
                @elseif (Auth::user()->hasRole('ustadz'))
                    <!-- Jika yang login adalah ustadz -->
                    <li class="{{ request()->routeIs('santri*') ? 'active' : '' }}">
                        <a href="{{ route('santri.index') }}" class="nav-link">
                            <i class="fas fa-users"></i><span>Data Santri</span>
                        </a>
                    </li>
                @endif
            @endcan

            <!-- Roles & Permissions - Only for Admin -->
            @if (Auth::user()->hasRole('admin'))
                <li class="menu-header">Roles & Permissions</li>

                @can('roles.view')
                    <li class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <a href="{{ route('roles.index') }}" class="nav-link">
                            <i class="fas fa-user-cog"></i><span>Roles</span>
                        </a>
                    </li>
                @endcan

                @can('permissions.view')
                    <li class="{{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                        <a href="{{ route('permissions.index') }}" class="nav-link">
                            <i class="fas fa-key"></i><span>Permissions</span>
                        </a>
                    </li>
                @endcan
            @endif

            <!-- Menu Data Pengguna - Only for Admin -->
            @can('user.view')
                <li class="menu-header">User Management</li>
                <li class="{{ request()->routeIs('user*') ? 'active' : '' }}">
                    <a href="{{ route('user.index') }}" class="nav-link">
                        <i class="fas fa-user-cog"></i><span>Data Pengguna</span>
                    </a>
                </li>
            @endcan

            <li class="menu-header">Keuangan</li>

            <!-- Menu Biaya -->
            @canany(['biaya-santri.view', 'daftar-biaya.view', 'kategori-biaya.view'])
                <li
                    class="dropdown {{ request()->routeIs('biaya-santris*') || request()->routeIs('daftar-biayas*') || request()->routeIs('kategori-biayas*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-money-bill-alt"></i> <span>Master Biaya</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('biaya-santri.view')
                            <li class="{{ request()->routeIs('biaya-santris*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('biaya-santris.index') }}">
                                    <i class="fas fa-user-graduate"></i> Biaya Santri
                                </a>
                            </li>
                        @endcan

                        @can('daftar-biaya.view')
                            <li class="{{ request()->routeIs('daftar-biayas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('daftar-biayas.index') }}">
                                    <i class="fas fa-list-ul"></i> Daftar Biaya
                                </a>
                            </li>
                        @endcan

                        @can('kategori-biaya.view')
                            <li class="{{ request()->routeIs('kategori-biayas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('kategori-biayas.index') }}">
                                    <i class="fas fa-tags"></i> Kategori Biaya
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Menu Tagihan -->
            @canany(['tagihan-terjadwal.view', 'tagihan-bulanan.view', 'tambahan-bulanan.view'])
                <li
                    class="dropdown {{ request()->routeIs('tagihan_bulanan*') || request()->routeIs('tagihan_terjadwal*') || request()->routeIs('tambahan_bulanan*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice-dollar"></i> <span>Tagihan</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('tagihan-terjadwal.view')
                            <li class="{{ request()->routeIs('tagihan_terjadwal*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('tagihan_terjadwal.index') }}">
                                    <i class="fas fa-calendar-check"></i> Tagihan Terjadwal
                                </a>
                            </li>
                        @endcan

                        @can('tagihan-bulanan.view')
                            <li class="{{ request()->routeIs('tagihan_bulanan*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('tagihan_bulanan.index') }}">
                                    <i class="fas fa-calendar-alt"></i> Tagihan Bulanan
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Menu Pembayaran -->
            @canany(['pembayaran.list', 'pembayaran.create', 'pembayaran.view', 'pembayaran.history'])
                <li class="dropdown {{ request()->routeIs('pembayaran*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-money-check"></i> <span>Pembayaran</span>
                    </a>
                    <ul class="dropdown-menu">
                        @canany(['pembayaran.list', 'pembayaran.create'])
                            <li
                                class="{{ request()->routeIs('pembayaran.index') || request()->routeIs('pembayaran.show') ? 'active' : '' }}">
                                <a href="{{ route('pembayaran.index') }}" class="nav-link">
                                    <i class="fas fa-money-bill-wave"></i> Pembayaran
                                </a>
                            </li>
                        @endcanany

                        @can('pembayaran.history')
                            <li class="{{ request()->routeIs('pembayaran.history') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('pembayaran.history') }}">
                                    <i class="fas fa-history"></i> Riwayat
                                </a>
                            </li>
                        @endcan

                        {{-- @if (Auth::user()->hasRole('admin'))
                            @can('pembayaran.bulk')
                                <li class="{{ request()->routeIs('pembayaran.bulk*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('pembayaran.bulk.index') }}">
                                        <i class="fas fa-layer-group"></i> Bulk
                                    </a>
                                </li>
                            @endcan
                        @endif --}}
                    </ul>
                </li>
            @endcanany

            <li class="menu-header">Madrasah Diniyah</li>

            <!-- Menu Kurikulum -->
            @canany(['kelas.view', 'mapel.view', 'mapel-kelas.view', 'tahun-ajar.view', 'qori-kelas.view',
                'riwayat-kelas.view'])
                <li
                    class="dropdown {{ request()->routeIs('mapel*') || request()->routeIs('kelas*') || request()->routeIs('mapel_kelas*') || request()->routeIs('tahun_ajar*') || request()->routeIs('qori_kelas*') || request()->routeIs('riwayat-kelas*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-graduation-cap"></i> <span>Kurikulum</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('riwayat-kelas.view')
                            <li class="{{ request()->routeIs('riwayat-kelas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('riwayat-kelas.index') }}">
                                    <i class="fas fa-clock"></i> Riwayat Kelas
                                </a>
                            </li>
                        @endcan

                        @can('mapel-kelas.view')
                            <li class="{{ request()->routeIs('mapel_kelas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('mapel_kelas.index') }}">
                                    <i class="fas fa-chalkboard-teacher"></i> Mapel Kelas
                                </a>
                            </li>
                        @endcan

                        @can('kelas.view')
                            <li class="{{ request()->routeIs('kelas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('kelas.index') }}">
                                    <i class="fas fa-door-open"></i> Kelas
                                </a>
                            </li>
                        @endcan

                        @can('tahun-ajar.view')
                            <li class="{{ request()->routeIs('tahun_ajar*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('tahun_ajar.index') }}">
                                    <i class="fas fa-calendar"></i> Tahun Ajar
                                </a>
                            </li>
                        @endcan

                        @can('mapel.view')
                            <li
                                class="{{ request()->routeIs('mapel.index') && !request()->routeIs('mapel_kelas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('mapel.index') }}">
                                    <i class="fas fa-book-open"></i> Mata Pelajaran
                                </a>
                            </li>
                        @endcan

                        @can('qori-kelas.view')
                            <li class="{{ request()->routeIs('qori_kelas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('qori_kelas.index') }}">
                                    <i class="fas fa-quran"></i> Qori Kelas
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Menu Ustadz -->
            {{-- @canany(['ustadz.view', 'penugasan-ustadz.view'])
                <li class="dropdown {{ request()->routeIs('ustadz*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-user-tie"></i> <span>Ustadz</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('ustadz.view')
                            <li class="{{ request()->routeIs('ustadz.get') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('ustadz.get') }}">
                                    <i class="fas fa-users"></i> Data Ustadz
                                </a>
                            </li>
                        @endcan

                        @can('penugasan-ustadz.view')
                            <li class="{{ request()->routeIs('ustadz.penugasan*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('ustadz.penugasan.index') }}">
                                    <i class="fas fa-tasks"></i> Penugasan Ustadz
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany --}}

            <!-- Menu Absensi - Commented out as requested in original -->
            {{-- @can('absensi.view')
                <li class="dropdown {{ request()->routeIs('absensi*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-clipboard-check"></i> <span>Absensi</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ request()->routeIs('absensi.index') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('absensi.index') }}">
                                <i class="fas fa-check-square"></i> Absensi
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan --}}

        </ul>
    </aside>
</div>
