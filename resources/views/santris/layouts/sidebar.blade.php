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
            <li class="{{ request()->routeIs('home*') ? 'active' : '' }}">
                <a href="{{ route('santri.dashboard') }}" class="nav-link">
                    <i class="fas fa-home"></i><span>Home</span>
                </a>
            </li>
            <li class="menu-header">Keuangan</li>
            <li class="dropdown {{ request()->routeIs('santri.tambahan_bulanan*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                        class="fas fa-file-invoice"></i> <span>Tambahan Bulanan</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->routeIs('santri.tambahan_bulanan.item_santri*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('santri.tambahan_bulanan.item_santri') }}">Item Tambahan
                            Santri</a>
                    </li>
                </ul>
            </li>
            <li
                class="dropdown {{ request()->routeIs('santri.tagihan_bulanan*') || request()->routeIs('santri.tagihan_terjadwal*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                        class="fas fa-file-invoice"></i> <span>Tagihan</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->routeIs('santri.tagihan_terjadwal*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('santri.tagihan_terjadwal.index') }}">Tagihan Terjadwal</a>
                    </li>
                    <li class="{{ request()->routeIs('santri.tagihan_bulanan*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('santri.tagihan_bulanan.index') }}">Tagihan Bulanan</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown {{ request()->routeIs('santri.pembayaran*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                        class="fas fa-file-invoice"></i> <span>Pembayaran</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->routeIs('santri.pembayaran.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('santri.pembayaran.index') }}">Pembayaran Tagihan</a>
                    </li>
                    <li class="{{ request()->routeIs('santri.pembayaran.riwayat') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('santri.pembayaran.riwayat') }}">Riwayat Pembayaran</a>
                    </li>
                </ul>
            </li>
        </ul>
    </aside>
</div>
