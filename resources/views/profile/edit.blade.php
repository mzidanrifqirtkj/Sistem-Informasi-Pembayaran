@extends('layouts.home')

@section('content')
    <div class="container">
        <h2 class="text-center mb-4">Edit Profil</h2>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if (session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <div class="card mx-auto" style="max-width: 800px;">
            <div class="card-body">
                <!-- Informasi Profil (Read Only) -->
                <div class="mb-4">
                    <h5 class="card-title mb-3">Informasi Profil</h5>
                    <div class="row">
                        @if ($user->santri)
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama:</label>
                                <p class="form-control-plaintext border rounded p-2 bg-light">
                                    {{ $user->santri->nama_santri }}</p>
                            </div>
                        @endif

                        @if ($user->santri && $user->santri->nis)
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">NIS:</label>
                                <p class="form-control-plaintext border rounded p-2 bg-light">{{ $user->santri->nis }}</p>
                            </div>
                        @endif

                        @if ($user->email)
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Email Saat Ini:</label>
                                <p class="form-control-plaintext border rounded p-2 bg-light">{{ $user->email }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <hr>

                <!-- Form Edit Email -->
                <div class="mb-4">
                    <h5 class="card-title mb-3">Edit Email</h5>
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Baru</label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" placeholder="Masukkan email baru">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
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
                    <form method="POST" action="{{ route('profile.update_password') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Lama</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    placeholder="Masukkan password lama" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password"
                                    data-target="current_password">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="new_password" id="new_password"
                                    class="form-control @error('new_password') is-invalid @enderror"
                                    placeholder="Masukkan password baru" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password"
                                    data-target="new_password">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            @error('new_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
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
@endsection

@section('script')
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
@endsection
