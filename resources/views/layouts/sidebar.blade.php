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
            @can('view_dashboard')
                <li class="{{ request()->routeIs('home*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i><span>Home</span>
                    </a>
                </li>
            @endcan

            <!-- Menu Data Santri -->
            @can('view_santri')
                @if (Auth::user()->hasRole('admin'))
                    <!-- Jika yang login adalah admin -->
                    <li class="{{ request()->routeIs('santri*') ? 'active' : '' }}">
                        <a href="{{ route('santri.index') }}" class="nav-link">
                            <i class="fas fa-users"></i><span>Data Santri</span>
                        </a>
                    </li>
                @elseif (Auth::user()->hasRole('santri'))
                    <!-- Jika yang login adalah santri -->
                    <li class="{{ request()->routeIs('santri.show') ? 'active' : '' }}">
                        <a href="{{ route('santri.show', Auth::user()->santri->id_santri) }}" class="nav-link">
                            <i class="fas fa-users"></i><span>Data Santri</span>
                        </a>
                    </li>
                @endif
            @endcan

            <li class="menu-header">Roles & Permissions</li>

            <li class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <a href="{{ route('roles.index') }}" class="nav-link">
                    <i class="fas fa-user-cog"></i><span>Roles</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                <a href="{{ route('permissions.index') }}" class="nav-link">
                    <i class="fas fa-key"></i><span>Permissions</span>
                </a>
            </li>

            @can('view_user')
                <li class="menu-header">User</li>
            @endcan

            <!-- Menu Data Pengguna -->
            @can('view_user')
                <li class="{{ request()->routeIs('user*') ? 'active' : '' }}">
                    <a href="{{ route('user.index') }}" class="nav-link">
                        <i class="fas fa-user-cog"></i><span>Data Pengguna</span>
                    </a>
                </li>
            @endcan

            <li class="menu-header">Keuangan</li>

            <!-- Menu Biaya -->
            @canany(['view_biaya_terjadwal', 'view_kategori'])
                <li class="dropdown {{ request()->routeIs('biaya_terjadwal*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-money-bill-alt"></i> <span>List Biaya</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ request()->routeIs('biaya-santris*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('biaya-santris.index') }}">
                                <i class="fas fa-user-graduate"></i> Biaya Santri
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('daftar-biayas*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('daftar-biayas.index') }}">
                                <i class="fas fa-list-ul"></i> Daftar Biaya
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('kategori-biayas*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('kategori-biayas.index') }}">
                                <i class="fas fa-tags"></i> Kategori Biaya
                            </a>
                        </li>
                    </ul>
                </li>
            @endcanany

            <!-- Menu Tagihan -->
            @canany(['view_tagihan_terjadwal', 'view_tagihan_bulanan'])
                <li
                    class="dropdown {{ request()->routeIs('tagihan_bulanan*') || request()->routeIs('tagihan_terjadwal*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice-dollar"></i> <span>Tagihan</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('view_tagihan_terjadwal')
                            <li class="{{ request()->routeIs('tagihan_terjadwal*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('tagihan_terjadwal.index') }}">
                                    <i class="fas fa-calendar-check"></i> Tagihan Terjadwal
                                </a>
                            </li>
                        @endcan
                        @can('view_tagihan_bulanan')
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
            @canany(['view_pembayaran', 'view_riwayat_pembayaran'])
                <li class="dropdown {{ request()->routeIs('pembayaran*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-money-check"></i> <span>Pembayaran</span>
                    </a>
                    <ul class="dropdown-menu">
                        @canany(['pembayaran-list', 'pembayaran-create'])
                            <li class="nav-item">
                                <a href="{{ route('pembayaran.index') }}" class="nav-link">
                                    <i class="fas fa-money-bill-wave"></i> Pembayaran
                                </a>
                            </li>
                        @endcanany
                        @can('view_riwayat_pembayaran')
                            <li class="{{ request()->routeIs('pembayaran.history') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('pembayaran.history') }}">
                                    <i class="fas fa-history"></i> Riwayat
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <li class="menu-header">Madrasah Diniyah</li>

            <!-- Menu Kurikulum -->
            @canany(['view_mapel_kelas', 'view_kelas', 'view_tahun_ajar', 'view_mapel'])
                <li
                    class="dropdown {{ request()->routeIs('mapel*') || request()->routeIs('kelas*') || request()->routeIs('mapel_kelas*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-graduation-cap"></i> <span>Kurikulum</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ request()->routeIs('riwayat-kelas*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('riwayat-kelas.index') }}">
                                <i class="fas fa-clock"></i> Riwayat Kelas
                            </a>
                        </li>
                        @can('view_mapel_kelas')
                            <li class="{{ request()->routeIs('mapel_kelas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('mapel_kelas.index') }}">
                                    <i class="fas fa-chalkboard-teacher"></i> Mapel Kelas
                                </a>
                            </li>
                        @endcan
                        @can('view_kelas')
                            <li class="{{ request()->routeIs('kelas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('kelas.index') }}">
                                    <i class="fas fa-door-open"></i> Kelas
                                </a>
                            </li>
                        @endcan
                        @can('view_tahun_ajar')
                            <li class="{{ request()->routeIs('tahun_ajar*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('tahun_ajar.index') }}">
                                    <i class="fas fa-calendar"></i> Tahun Ajar
                                </a>
                            </li>
                        @endcan
                        @can('view_mapel')
                            <li class="{{ request()->routeIs('mapel*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('mapel.index') }}">
                                    <i class="fas fa-book-open"></i> Mata Pelajaran
                                </a>
                            </li>
                        @endcan

                        <li class="{{ request()->routeIs('qori_kelas*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('qori_kelas.index') }}">
                                <i class="fas fa-quran"></i> Qori Kelas
                            </a>
                        </li>
                    </ul>
                </li>
            @endcanany

            {{-- <!-- Menu Ustadz -->
            @canany(['view_ustadz', 'view_penugasan_ustadz'])
                <li class="dropdown {{ request()->routeIs('ustadz*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-user-tie"></i> <span>Ustadz</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('view_ustadz')
                            <li class="{{ request()->routeIs('ustadz.get') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('ustadz.get') }}">
                                    <i class="fas fa-users"></i> Data Ustadz
                                </a>
                            </li>
                        @endcan
                        @can('view_penugasan_ustadz')
                            <li class="{{ request()->routeIs('ustadz.penugasan*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('ustadz.penugasan.index') }}">
                                    <i class="fas fa-tasks"></i> Penugasan Ustadz
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Menu Absensi -->
            @can('view_absensi')
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
