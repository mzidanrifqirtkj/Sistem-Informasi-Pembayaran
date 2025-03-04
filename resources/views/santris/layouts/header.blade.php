<ul class="navbar-nav navbar-right">
    <li class="dropdown">
        {{-- <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            @if (Auth::user()->santri_id == null || Auth::user()->santris->photo == null)
            <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
        @else
        <img alt="image" src="{{ asset('storage/photo/' . Auth::user()->santris->photo) }}" class="rounded-circle mr-1"
            style="position: relative;width: 30px;height: 30px;overflow: hidden;">
        @endif
        <div class="d-sm-none d-lg-inline-block">{{ Auth::user()->santris->name }}</div>
        </a> --}}

        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">
                {{ auth()->user()->santri->nama_santri ?? 'Santri' }}
            </div>
        </a>

        {{-- <div class="dropdown-menu dropdown-menu-right">
            <a href="{{ route('santri.show', Auth::user()->santris->id) }}" class="dropdown-item has-icon">
        <i class="fas fa-user"></i> Profil
        </a>

        <div class="dropdown-divider"></div>

        <a class="dropdown-item has-icon text-danger" href="{{ route('logout') }}#"
            onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
        </a>

        <form id="logout-form" action="{{ route('logout') }}#" method="POST" class="d-none">
            @csrf
        </form>
</div> --}}
        <div class="dropdown-menu dropdown-menu-right">
            <a href="{{ route('santri.profile.edit') }}" class="dropdown-item has-icon">
                <i class="fas fa-user"></i> Profil
            </a>

            <div class="dropdown-divider"></div>

            <a class="dropdown-item has-icon text-danger" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </li>
</ul>
