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
            <li class="{{ (request()->routeIs('home*')) ? 'active' : '' }}">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="fas fa-home"></i><span>Home</span>
                </a>
            </li>
            <li class="{{ (request()->routeIs('admin.santri*')) ? 'active' : '' }}">
                <a href="{{ route('admin.santri.index') }}" class="nav-link">
                    <i class="fas fa-users"></i><span>Data Santri</span>
                </a>
            </li>
            <li class="menu-header">User</li>
            <li class="{{ (request()->routeIs('admin.user*')) ? 'active' : '' }}">
                <a href="{{ route('admin.user.index') }}" class="nav-link">
                    <i class="fas fa-user-cog"></i><span>Data Pengguna</span>
                </a>
            </li>
            <li class="menu-header">Keuangan</li>
            <li class="dropdown {{ (request()->routeIs('admin.biaya_terjadwal*')) ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-file-invoice"></i> <span>Biaya</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ (request()->routeIs('admin.biaya_terjadwal*')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.biaya_terjadwal.index') }}">Biaya Terjadwal</a>
                    </li>
                    <li class="{{ (request()->routeIs('admin.kategori*')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.kategori.index') }}">Biaya Bulanan</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown {{ (request()->routeIs('admin.tambahan_bulanan*')) ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-file-invoice"></i> <span>Tambahan Bulanan</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ (request()->routeIs('admin.tambahan_bulanan.index')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.tambahan_bulanan.index') }}">Item Tambahan</a>
                    </li>
                    <li class="{{ (request()->routeIs('admin.tambahan_bulanan.item_santri*')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.tambahan_bulanan.item_santri') }}">Item Tambahan Santri</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown {{ (request()->routeIs('admin.tagihan_bulanan*') || request()->routeIs('admin.tagihan_terjadwal*')) ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-file-invoice"></i> <span>Tagihan</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ (request()->routeIs('admin.tagihan_terjadwal*')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.tagihan_terjadwal.index') }}">Tagihan Terjadwal</a>
                    </li>
                    <li class="{{ (request()->routeIs('admin.tagihan_bulanan*')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.tagihan_bulanan.index') }}">Tagihan Bulanan</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown {{ (request()->routeIs('admin.pembayaran*')) ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-file-invoice"></i> <span>Pembayaran</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ (request()->routeIs('admin.pembayaran.index')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.pembayaran.index') }}">Pembayaran Tagihan</a>
                    </li>
                    <li class="{{ (request()->routeIs('admin.pembayaran.riwayat')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.pembayaran.riwayat') }}">Riwayat Pembayaran</a>
                    </li>
                </ul>
            </li>


            <li class="menu-header">Pendidikan</li>
            <li class="{{ (request()->routeIs('admin.kelas*')) ? 'active' : '' }}">
                <a href="{{ route('admin.kelas.index') }}" class="nav-link">
                    <i class="fas fa-home"></i><span>Data Kelas</span>
                </a>
            </li>
            <li class="{{ (request()->routeIs('admin.tahun_ajar*')) ? 'active' : '' }}">
                <a href="{{ route('admin.tahun_ajar.index') }}" class="nav-link">
                    <i class="fas fa-home"></i><span>Tahun Ajar</span>
                </a>
            </li>
            <li class="{{ (request()->routeIs('admin.mapel*')) ? 'active' : '' }}">
                <a href="{{ route('admin.mapel.index') }}" class="nav-link">
                    <i class="fas fa-home"></i><span>Daftar Mapel</span>
                </a>
            </li>
            <li class="dropdown {{ (request()->routeIs('admin.ustadz*')) ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-file-invoice"></i> <span>Ustadz</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ (request()->routeIs('admin.ustadz.get')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.ustadz.get') }}">Data Ustadz</a>
                    </li>
                    <li class="{{ (request()->routeIs('admin.ustadz.penugasan*')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.ustadz.penugasan.index') }}">Penugasan Ustadz</a>
                    </li>
                </ul>
            </li>
            {{-- <li class="{{ (request()->routeIs('buku-kas*')) ? 'active' : '' }}">
                <a href="{{ route('buku-kas.index') }}" class="nav-link">
                    <i class="fas fa-book-open"></i><span>Buku Kas</span>
                </a>
            </li>
            <li class="menu-header">Administrasi</li>
            <li class="{{ (request()->routeIs('surat-masuk*')) ? 'active' : '' }}">
                <a href="{{ route('surat-masuk.index') }}" class="nav-link">
                    <i class="fas fa-envelope"></i><span>Surat Masuk</span>
                </a>
            </li>
            <li class="{{ (request()->routeIs('surat-keluar*')) ? 'active' : '' }}">
                <a href="{{ route('surat-keluar.index') }}" class="nav-link">
                    <i class="fas fa-envelope-open-text"></i><span>Surat Keluar</span>
                </a>
            </li>
            <li class="menu-header">Logs</li>
            <li class="{{ (request()->routeIs('logs.index')) ? 'active' : '' }}">
                <a href="{{ route('logs.index') }}" class="nav-link">
                    <i class="fas fa-history"></i><span>Log Aktivitas</span>
                </a>
            </li> --}}
        </ul>
    </aside>
</div>
