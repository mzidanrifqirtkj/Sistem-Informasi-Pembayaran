<ul class="navbar-nav navbar-right">
    <li class="dropdown">
        

        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <?php if(Auth::user()->hasRole('admin')): ?>
                <!-- Jika yang login adalah admin -->
                <img alt="image" src="<?php echo e(asset('assets/img/avatar/avatar-1.png')); ?>" class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">Admin Pondok</div>
            <?php elseif(Auth::user()->hasRole('santri')): ?>
                <!-- Jika yang login adalah santri -->
                <?php if(Auth::user()->santri && Auth::user()->santri->photo): ?>
                    <!-- Jika santri memiliki foto -->
                    <img alt="image" src="<?php echo e(asset('storage/photo/' . Auth::user()->santri->photo)); ?>"
                        class="rounded-circle mr-1"
                        style="position: relative;width: 30px;height: 30px;overflow: hidden;">
                <?php else: ?>
                    <!-- Jika santri tidak memiliki foto -->
                    <img alt="image" src="<?php echo e(asset('assets/img/avatar/avatar-1.png')); ?>" class="rounded-circle mr-1">
                <?php endif; ?>
                <div class="d-sm-none d-lg-inline-block"><?php echo e(Auth::user()->santri->nama_santri); ?></div>
            <?php endif; ?>
        </a>

        
        <div class="dropdown-menu dropdown-menu-right">
            <?php if(Auth::user()->hasRole('admin')): ?>
                <!-- Jika yang login adalah admin -->
                <a href="<?php echo e(route('profile.edit')); ?>" class="dropdown-item has-icon">
                    <i class="fas fa-user"></i> Profil
                </a>
            <?php elseif(Auth::user()->hasRole('santri')): ?>
                <!-- Jika yang login adalah santri -->
                <a href="<?php echo e(route('profile.edit', Auth::user()->santri->nis)); ?>" class="dropdown-item has-icon">
                    <i class="fas fa-user"></i> Profil
                </a>
            <?php endif; ?>

            <div class="dropdown-divider"></div>

            <a class="dropdown-item has-icon text-danger" href="<?php echo e(route('logout')); ?>"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>

            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                <?php echo csrf_field(); ?>
            </form>
        </div>
    </li>
</ul>
<?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/layouts/header.blade.php ENDPATH**/ ?>