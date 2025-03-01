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
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i><span>Home</span>
                    </a>
                </li>
            @endcan

            <!-- Menu Data Santri -->
            @can('view_santri')
                @if (Auth::user()->hasRole('admin'))
                    <!-- Jika yang login adalah admin -->
                    <li class="{{ request()->routeIs('admin.santri*') ? 'active' : '' }}">
                        <a href="{{ route('admin.santri.index') }}" class="nav-link">
                            <i class="fas fa-users"></i><span>Data Santri</span>
                        </a>
                    </li>
                @elseif (Auth::user()->hasRole('santri'))
                    <!-- Jika yang login adalah santri -->
                    <li class="{{ request()->routeIs('admin.santri.show') ? 'active' : '' }}">
                        <a href="{{ route('admin.santri.show', Auth::user()->santri->id_santri) }}" class="nav-link">
                            <i class="fas fa-users"></i><span>Data Santri</span>
                        </a>
                    </li>
                @endif
            @endcan

            @can('view_user')
                <li class="menu-header">User</li>
            @endcan

            <!-- Menu Data Pengguna -->
            @can('view_user')
                <li class="{{ request()->routeIs('admin.user*') ? 'active' : '' }}">
                    <a href="{{ route('admin.user.index') }}" class="nav-link">
                        <i class="fas fa-user-cog"></i><span>Data Pengguna</span>
                    </a>
                </li>
            @endcan

            <li class="menu-header">Keuangan</li>

            <!-- Menu Biaya -->
            @canany(['view_biaya_terjadwal', 'view_kategori'])
                <li class="dropdown {{ request()->routeIs('admin.biaya_terjadwal*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Biaya</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('view_biaya_terjadwal')
                            <li class="{{ request()->routeIs('admin.biaya_terjadwal*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.biaya_terjadwal.index') }}">Biaya Terjadwal</a>
                            </li>
                        @endcan
                        @can('view_kategori')
                            <li class="{{ request()->routeIs('admin.kategori*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.kategori.index') }}">Biaya Bulanan</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Menu Tambahan Bulanan -->
            @canany(['view_tambahan_bulanan', 'view_item_santri'])
                <li class="dropdown {{ request()->routeIs('admin.tambahan_bulanan*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Tambahan Bulanan</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('view_tambahan_bulanan')
                            <li class="{{ request()->routeIs('admin.tambahan_bulanan.index') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.tambahan_bulanan.index') }}">Item Tambahan</a>
                            </li>
                        @endcan
                        @can('view_item_santri')
                            <li class="{{ request()->routeIs('admin.tambahan_bulanan.item_santri*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.tambahan_bulanan.item_santri') }}">Item Tambahan
                                    Santri</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Menu Tagihan -->
            @canany(['view_tagihan_terjadwal', 'view_tagihan_bulanan'])
                <li
                    class="dropdown {{ request()->routeIs('admin.tagihan_bulanan*') || request()->routeIs('admin.tagihan_terjadwal*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Tagihan</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('view_tagihan_terjadwal')
                            <li class="{{ request()->routeIs('admin.tagihan_terjadwal*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.tagihan_terjadwal.index') }}">Tagihan Terjadwal</a>
                            </li>
                        @endcan
                        @can('view_tagihan_bulanan')
                            <li class="{{ request()->routeIs('admin.tagihan_bulanan*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.tagihan_bulanan.index') }}">Tagihan Bulanan</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Menu Pembayaran -->
            @canany(['view_pembayaran', 'view_riwayat_pembayaran'])
                <li class="dropdown {{ request()->routeIs('admin.pembayaran*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Pembayaran</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('view_pembayaran')
                            <li class="{{ request()->routeIs('admin.pembayaran.index') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.pembayaran.index') }}">Pembayaran Tagihan</a>
                            </li>
                        @endcan
                        @can('view_riwayat_pembayaran')
                            <li class="{{ request()->routeIs('admin.pembayaran.riwayat') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.pembayaran.riwayat') }}">Riwayat Pembayaran</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <li class="menu-header">Madrasah Diniyah</li>

            <!-- Menu Kurikulum -->
            @canany(['view_mapel_kelas', 'view_kelas', 'view_tahun_ajar', 'view_mapel'])
                <li
                    class="dropdown {{ request()->routeIs('admin.mapel*') || request()->routeIs('admin.kelas*') || request()->routeIs('admin.mapel_kelas*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Kurikulum</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('view_mapel_kelas')
                            <li class="{{ request()->routeIs('admin.mapel_kelas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.mapel_kelas.index') }}">Mapel Kelas</a>
                            </li>
                        @endcan
                        @can('view_kelas')
                            <li class="{{ request()->routeIs('admin.kelas*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.kelas.index') }}">Kelas</a>
                            </li>
                        @endcan
                        @can('view_tahun_ajar')
                            <li class="{{ request()->routeIs('admin.tahun_ajar*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.tahun_ajar.index') }}">Tahun Ajar</a>
                            </li>
                        @endcan
                        @can('view_mapel')
                            <li class="{{ request()->routeIs('admin.mapel*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.mapel.index') }}">Daftar Mapel</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Menu Ustadz -->
            @canany(['view_ustadz', 'view_penugasan_ustadz'])
                <li class="dropdown {{ request()->routeIs('admin.ustadz*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Ustadz</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('view_ustadz')
                            <li class="{{ request()->routeIs('admin.ustadz.get') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.ustadz.get') }}">Data Ustadz</a>
                            </li>
                        @endcan
                        @can('view_penugasan_ustadz')
                            <li class="{{ request()->routeIs('admin.ustadz.penugasan*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.ustadz.penugasan.index') }}">Penugasan Ustadz</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Menu Absensi -->
            @can('view_absensi')
                <li class="dropdown {{ request()->routeIs('admin.absensi*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice"></i> <span>Absensi</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ request()->routeIs('admin.absensi.index') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.absensi.index') }}">Absensi</a>
                        </li>
                    </ul>
                </li>
            @endcan
        </ul>
    </aside>
</div>
