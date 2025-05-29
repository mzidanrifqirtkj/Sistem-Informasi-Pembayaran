<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title_page'); ?></title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(asset('assets/img/logo.ico')); ?>" type="image/x-icon">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/modules/bootstrap/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/modules/fontawesome/css/all.min.css')); ?>">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/modules/select2/dist/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/modules/datatables/datatables.min.css')); ?>">
    <link rel="stylesheet"
        href="<?php echo e(asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')); ?>">

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/components.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ponpes-style.css')); ?>">

    <!-- DataTables CSS -->
    

    <!-- CSS Custom -->
    <style>
        #cash-table tbody tr td {
            text-align: center;
        }
    </style>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css"
        rel="stylesheet">

    

    
    

    <?php echo $__env->yieldContent('css_inline'); ?>
</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <form class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i
                                    class="fas fa-bars"></i></a></li>
                    </ul>
                </form>
                <?php echo $__env->make('layouts.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </nav>

            <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1><?php echo $__env->yieldContent('title_page'); ?></h1>
                    </div>
                    <?php if(Session::has('alert')): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <?php echo e(Session::get('alert')); ?>

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php elseif(Session::has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e(Session::get('error')); ?>

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php elseif(Session::has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(Session::get('success')); ?>

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="section-body">
                        <div class="card">
                            <div class="p-3">
                                <?php echo $__env->yieldContent('content'); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- End Main Content -->

            <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <?php echo $__env->yieldContent('modal'); ?>

    <!-- General JS Scripts -->
    <script src="<?php echo e(asset('assets/modules/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/modules/bootstrap/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/modules/nicescroll/jquery.nicescroll.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/modules/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/stisla.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/digital-sign.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/modules/select2/dist/js/select2.full.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/modules/datatables/datatables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/modules/jquery-ui/jquery-ui.min.js')); ?>"></script>

    <!-- DataTables JS -->
    

    <!-- Page Specific JS File -->
    <?php echo $__env->yieldContent('script'); ?>

    <!-- Template JS File -->
    <script src="<?php echo e(asset('assets/js/scripts.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/custom.js')); ?>"></script>

    <script>
        $('.alert').alert();
    </script>
    
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.min.js"></script>
    
    

</body>

</html>
<?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/layouts/home.blade.php ENDPATH**/ ?>