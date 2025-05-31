<?php $__env->startSection('content'); ?>
    <div class="container">
        <h2 class="text-center mb-4">Edit Profil</h2>

        <?php if(session('status')): ?>
            <div class="alert alert-success"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <?php if(session('info')): ?>
            <div class="alert alert-info"><?php echo e(session('info')); ?></div>
        <?php endif; ?>

        <div class="card mx-auto" style="max-width: 800px;">
            <div class="card-body">
                <!-- Informasi Profil (Read Only) -->
                <div class="mb-4">
                    <h5 class="card-title mb-3">Informasi Profil</h5>
                    <div class="row">
                        <?php if($user->santri): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama:</label>
                                <p class="form-control-plaintext border rounded p-2 bg-light">
                                    <?php echo e($user->santri->nama_santri); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if($user->santri && $user->santri->nis): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">NIS:</label>
                                <p class="form-control-plaintext border rounded p-2 bg-light"><?php echo e($user->santri->nis); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if($user->email): ?>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Email Saat Ini:</label>
                                <p class="form-control-plaintext border rounded p-2 bg-light"><?php echo e($user->email); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <!-- Form Edit Email -->
                <div class="mb-4">
                    <h5 class="card-title mb-3">Edit Email</h5>
                    <form method="POST" action="<?php echo e(route('profile.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Baru</label>
                            <input type="email" name="email" id="email"
                                class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('email', $user->email)); ?>" placeholder="Masukkan email baru">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah email</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Perbarui Email
                        </button>
                    </form>
                </div>

                <hr>

                <!-- Form Ganti Password -->
                <div class="mb-4">
                    <h5 class="card-title mb-3">Ganti Password</h5>
                    <form method="POST" action="<?php echo e(route('profile.update_password')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Lama</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password"
                                    class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Masukkan password lama" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password"
                                    data-target="current_password">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="new_password" id="new_password"
                                    class="form-control <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Masukkan password baru" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password"
                                    data-target="new_password">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Password minimal 8 karakter</small>
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                    class="form-control" placeholder="Konfirmasi password baru" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password"
                                    data-target="new_password_confirmation">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-key me-1"></i>Perbarui Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                let input = document.getElementById(this.getAttribute('data-target'));
                let icon = this.querySelector('i');
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    input.type = "password";
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/profile/edit.blade.php ENDPATH**/ ?>