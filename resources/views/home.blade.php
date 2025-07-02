@extends('layouts.home')
@section('title_page', 'Dashboard')
@section('content')
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Selamat Datang di Sistem Informasi Pondok Pesantren Al Luqmaniyyah</h4>
                </div>
                <div class="card-body">
                    <!-- Panel Panduan dan Info -->
                    <div class="row mb-4">
                        {{-- Panduan untuk Santri --}}
                        @role('santri')
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="alert alert-primary d-flex align-items-center">
                                    <i class="fas fa-graduation-cap fa-2x mr-4"></i>
                                    <div class="flex-fill">
                                        <strong>Panduan Santri</strong><br>
                                        <small>Cara menggunakan sistem untuk santri</small>
                                    </div>
                                    <a href="https://docs.google.com/document/d/1BaV0vaMYN1IItZIcihTnyn0Fy-GfgI60L8pecwX1X6g/edit?usp=sharing"
                                        target="_blank" class="btn btn-sm btn-primary ml-3">
                                        <i class="fas fa-play-circle mr-1"></i> Buka
                                    </a>
                                </div>
                            </div>
                        @endrole

                        {{-- Panduan untuk Admin --}}
                        @role('admin')
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="alert alert-warning d-flex align-items-center">
                                    <i class="fas fa-tools fa-2x mr-4"></i>
                                    <div class="flex-fill">
                                        <strong>Panduan Admin</strong><br>
                                        <small>Cara mengelola sistem sebagai admin</small>
                                    </div>
                                    <a href="https://docs.google.com/document/d/11IO3ZzwaKf3kkzSlOZBa67CvuhITfDruz4-6QTwZCpE/edit?usp=drive_link"
                                        target="_blank" class="btn btn-sm btn-warning ml-3 text-white">
                                        <i class="fas fa-play-circle mr-1"></i> Buka
                                    </a>
                                </div>
                            </div>
                        @endrole
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="fas fa-file-alt mr-4 fa-2x"></i>
                                <div class="flex-fill">
                                    <strong>Brosur Pondok</strong><br>
                                    <small>Informasi pendaftaran & biaya</small>
                                </div>
                                <a href="https://alluqmaniyyah.id/brosur-2023" target="_blank"
                                    class="btn btn-sm btn-success ml-3">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 mb-3">
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-info-circle mr-4 fa-2x"></i>
                                <div class="flex-fill">
                                    <strong>Bantuan & FAQ</strong><br>
                                    <small>Pertanyaan yang sering diajukan</small>
                                </div>
                                <button class="btn btn-sm btn-info ml-3" data-toggle="modal" data-target="#faqModal">
                                    <i class="fas fa-question mr-1"></i> FAQ
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Profil Pondok -->
                    <div class="row">
                        <!-- Kolom Kiri - Foto dan Info Dasar -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <img src="{{ asset('assets/img/pplq.jpg') }}" class="card-img-top"
                                    alt="Pondok Pesantren Al Luqmaniyyah" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Pondok Pesantren Al Luqmaniyyah</h5>
                                    <div class="text-center">
                                        <p class="card-text">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                            Dukuh Kalangan, Kel. Pandean<br>
                                            Kec. Umbulharjo, Yogyakarta<br>
                                            <small class="text-muted">Luas: 1.250 m²</small>
                                        </p>
                                        <p class="card-text">
                                            <i class="fas fa-user-graduate text-primary"></i> <strong>325
                                                Santri</strong><br>
                                            <small>170 Putra • 155 Putri</small>
                                        </p>
                                        <p class="card-text">
                                            <i class="fas fa-calendar-plus text-primary"></i> <strong>Didirikan:
                                                2000</strong><br>
                                            <small>Pendiri: H. Luqman Jamal Hasibuan</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Tengah - Sejarah -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-clock mr-3"></i>Sejarah Pondok</h6>
                                </div>
                                <div class="card-body">
                                    <div style="max-height: 350px; overflow-y: auto;">
                                        <p class="card-text text-justify">
                                            <strong>1998-1999:</strong> Pondok Pesantren Al Luqmaniyyah mulai dibangun
                                            atas prakarsa Bapak H. Luqman Jamal Hasibuan.
                                        </p>
                                        <p class="card-text text-justify">
                                            <strong>9 Februari 2000:</strong> Diresmikan dengan nama "Pondok Pesantren Salaf
                                            Putra Putri Asrama Perguruan Islam (API) Al Luqmaniyyah" oleh KH. Salimi
                                            (Pengasuh Pondok Pesantren As Salimiyyah).
                                        </p>
                                        <p class="card-text text-justify">
                                            <strong>Kepengasuhan:</strong>
                                        </p>
                                        <ul class="text-justify">
                                            <li><strong>2000-2011:</strong> KH. Najib Salimi</li>
                                            <li><strong>2011-2016:</strong> Nyai Hj. Siti Chamnah</li>
                                            <li><strong>2016-sekarang:</strong> KH. Na'imul Wa'in</li>
                                        </ul>
                                        <p class="card-text text-justify">
                                            Sistem pendidikan berkiblat pada API Tegalrejo, Magelang, dengan
                                            mewajibkan mujahadah setiap ba'da maghrib dan qobla subuh.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan - Visi Misi -->
                        <div class="col-lg-4 col-md-12 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-rocket mr-3"></i>Visi & Misi</h6>
                                </div>
                                <div class="card-body">
                                    <div style="max-height: 350px; overflow-y: auto;">
                                        <h6 class="text-primary">Visi</h6>
                                        <p class="card-text text-justify">
                                            "Tampil Unggul dan Berkualitas dalam Ilmu Agama dan Amal Shaleh Bagi Peradaban"
                                        </p>

                                        <h6 class="text-primary">Misi</h6>
                                        <ul class="card-text">
                                            <li>Mengkaji dan mengembangkan ilmu agama berbasis kitab-kitab mu'tabarah</li>
                                            <li>Melaksanakan kegiatan sosial secara aktif</li>
                                            <li>Meningkatkan peran pondok dalam menjawab permasalahan masyarakat</li>
                                            <li>Meningkatkan kepekaan santri dalam berinteraksi sosial</li>
                                        </ul>

                                        <h6 class="text-primary">Tujuan</h6>
                                        <ul class="card-text">
                                            <li>Menyiapkan santri dengan kemampuan keilmuan agama mendalam</li>
                                            <li>Menyiapkan santri sebagai kader bangsa yang tangguh dan berakhlak mulia</li>
                                            <li>Menyiapkan santri yang menghargai nilai-nilai ilmu agama dan kemanusiaan
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kurikulum dan Kegiatan -->
                    <div class="row mt-4">
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0"><i class="fas fa-book-open mr-3"></i>Kurikulum Pendidikan</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>6 Jenjang Pendidikan (2 semester per jenjang):</strong></p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item d-flex align-items-center px-0">
                                                    <span class="badge badge-primary mr-2">1</span> Jurumiyyah
                                                </li>
                                                <li class="list-group-item d-flex align-items-center px-0">
                                                    <span class="badge badge-primary mr-2">2</span> Imrithy
                                                </li>
                                                <li class="list-group-item d-flex align-items-center px-0">
                                                    <span class="badge badge-primary mr-2">3</span> Alfiyyah I
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item d-flex align-items-center px-0">
                                                    <span class="badge badge-success mr-2">4</span> Alfiyyah II
                                                </li>
                                                <li class="list-group-item d-flex align-items-center px-0">
                                                    <span class="badge badge-success mr-2">5</span> Takhtim Bukhori
                                                </li>
                                                <li class="list-group-item d-flex align-items-center px-0">
                                                    <span class="badge badge-success mr-2">6</span> Takhtim Ihya
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <p><strong>Kegiatan Penunjang:</strong></p>
                                        <div class="d-flex flex-wrap">
                                            <span class="badge badge-info mr-2 mb-2 px-2 py-1">Seni Baca Al-Qur'an</span>
                                            <span class="badge badge-info mr-2 mb-2 px-2 py-1">Seni Hadroh</span>
                                            <span class="badge badge-info mr-2 mb-2 px-2 py-1">Khitabah</span>
                                            <span class="badge badge-info mr-2 mb-2 px-2 py-1">Jurnalistik</span>
                                            <span class="badge badge-info mr-2 mb-2 px-2 py-1">Pengembangan TI</span>
                                            <span class="badge badge-info mr-2 mb-2 px-2 py-1">Olahraga</span>
                                            <span class="badge badge-info mr-2 mb-2 px-2 py-1">Kaligrafi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-calendar-check mr-3"></i>Agenda Tahunan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="text-primary mb-1"><i
                                                        class="fas fa-user-graduate mr-1"></i>Munaqosyah</h6>
                                                <p class="small mb-0">Ujian terbuka kitab Alfiyyah Ibnu Aqil untuk kelas
                                                    Alfiyyah II</p>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="text-success mb-1"><i class="fas fa-award mr-1"></i>Wisuda Ihya
                                                </h6>
                                                <p class="small mb-0">Penghargaan bagi santri yang menyelesaikan semua
                                                    jenjang pendidikan</p>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-warning"></div>
                                            <div class="timeline-content">
                                                <h6 class="text-warning mb-1"><i class="fas fa-star mr-1"></i>Haflah At
                                                    Tasyakkur</h6>
                                                <p class="small mb-0">Puncak acara syukuran khatam kitab (10 Sya'ban)</p>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-danger"></div>
                                            <div class="timeline-content">
                                                <h6 class="text-danger mb-1"><i class="fas fa-route mr-1"></i>Ziaroh Aulia
                                                </h6>
                                                <p class="small mb-0">Ziaroh ke makam Wali Songo dan Aulia di Jawa (3 tahun
                                                    periode)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fungsi Sistem -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h6 class="mb-0"><i class="fas fa-desktop mr-3"></i>Fungsi Sistem Informasi Pondok
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6 mb-3">
                                            <div class="text-center">
                                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-user-graduate fa-2x"></i>
                                                </div>
                                                <h6 class="font-weight-bold">Manajemen Santri</h6>
                                                <p class="text-muted small mb-0">Mengelola data santri, pendaftaran, dan
                                                    riwayat kelas</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 mb-3">
                                            <div class="text-center">
                                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-coins fa-2x"></i>
                                                </div>
                                                <h6 class="font-weight-bold">Sistem Keuangan</h6>
                                                <p class="text-muted small mb-0">Mengelola biaya, tagihan, dan pembayaran
                                                    santri</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 mb-3">
                                            <div class="text-center">
                                                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                                </div>
                                                <h6 class="font-weight-bold">Kurikulum</h6>
                                                <p class="text-muted small mb-0">Mengelola kelas, mata pelajaran, dan tahun
                                                    ajaran</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 mb-3">
                                            <div class="text-center">
                                                <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-users-cog fa-2x"></i>
                                                </div>
                                                <h6 class="font-weight-bold">User Management</h6>
                                                <p class="text-muted small mb-0">Mengelola pengguna, roles, dan permissions
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Modal -->
    <div class="modal fade" id="faqModal" tabindex="-1" role="dialog" aria-labelledby="faqModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="faqModalLabel">
                        <i class="fas fa-question-circle mr-3"></i>Frequently Asked Questions (FAQ)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="card border">
                            <div class="card-header" id="faq1">
                                <h6 class="mb-0">
                                    <button class="btn btn-link text-left w-100" type="button" data-toggle="collapse"
                                        data-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                        <i class="fas fa-chevron-right mr-3"></i>Bagaimana cara menggunakan sistem ini?
                                    </button>
                                </h6>
                            </div>
                            <div id="collapse1" class="collapse" aria-labelledby="faq1" data-parent="#faqAccordion">
                                <div class="card-body">
                                    <p>Sistem ini memiliki beberapa modul utama:</p>
                                    <ul>
                                        <li><strong>Data Santri:</strong> Untuk melihat dan mengelola informasi santri</li>
                                        <li><strong>Keuangan:</strong> Untuk mengelola pembayaran dan tagihan</li>
                                        <li><strong>Kurikulum:</strong> Untuk mengelola kelas dan mata pelajaran</li>
                                    </ul>
                                    <p class="mb-0">Gunakan menu sidebar untuk navigasi antar modul.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card border">
                            <div class="card-header" id="faq2">
                                <h6 class="mb-0">
                                    <button class="btn btn-link text-left w-100" type="button" data-toggle="collapse"
                                        data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                        <i class="fas fa-chevron-right mr-3"></i>Berapa biaya pendaftaran santri baru?
                                    </button>
                                </h6>
                            </div>
                            <div id="collapse2" class="collapse" aria-labelledby="faq2" data-parent="#faqAccordion">
                                <div class="card-body">
                                    <p>Biaya pendaftaran santri baru adalah <strong>Rp. 2.150.000</strong></p>
                                    <p class="mb-0">Untuk informasi lebih detail, silakan lihat brosur resmi di:
                                        <a href="https://alluqmaniyyah.id/brosur-2023" target="_blank"
                                            class="text-primary">
                                            https://alluqmaniyyah.id/brosur-2023
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card border">
                            <div class="card-header" id="faq3">
                                <h6 class="mb-0">
                                    <button class="btn btn-link text-left w-100" type="button" data-toggle="collapse"
                                        data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                        <i class="fas fa-chevron-right mr-3"></i>Apakah bisa sambil kuliah?
                                    </button>
                                </h6>
                            </div>
                            <div id="collapse3" class="collapse" aria-labelledby="faq3" data-parent="#faqAccordion">
                                <div class="card-body">
                                    <p><strong>Ya, bisa.</strong> Mayoritas santri Al Luqmaniyyah juga menempuh pendidikan
                                        formal di berbagai perguruan tinggi di Yogyakarta.</p>
                                    <p class="mb-0">Pondok ini memang didesain untuk mahasiswa yang ingin mendalami ilmu
                                        agama sambil
                                        melanjutkan pendidikan formal.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card border">
                            <div class="card-header" id="faq4">
                                <h6 class="mb-0">
                                    <button class="btn btn-link text-left w-100" type="button" data-toggle="collapse"
                                        data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                        <i class="fas fa-chevron-right mr-3"></i>Bagaimana cara reset password?
                                    </button>
                                </h6>
                            </div>
                            <div id="collapse4" class="collapse" aria-labelledby="faq4" data-parent="#faqAccordion">
                                <div class="card-body">
                                    <p>Untuk reset password, silakan hubungi administrator sistem atau:</p>
                                    <ol>
                                        <li>Klik tombol "Lupa Password" di halaman login</li>
                                        <li>Masukkan email yang terdaftar</li>
                                        <li>Cek email untuk link reset password</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css_inline')
    <style>
        /* Fix z-index conflict between SweetAlert2 and Bootstrap Modal */
        .swal2-container {
            z-index: 10000 !important;
            /* Higher than Bootstrap modal (1050) */
        }

        /* Ensure modal backdrop doesn't interfere */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        /* Fix untuk multiple backdrop */
        .modal-backdrop+.modal-backdrop {
            opacity: 0;
            display: none;
        }

        .modal-backdrop.show {
            opacity: 0 !important;
            /* Transparan sepenuhnya */
            pointer-events: none !important;
            background-color: transparent !important;
        }

        .card-img-top {
            transition: transform 0.3s ease;
        }

        .card-img-top:hover {
            transform: scale(1.05);
        }

        .alert {
            border-radius: 0.375rem;
            border: none;
        }

        .card {
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
            transition: all 0.15s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .timeline {
            position: relative;
            padding-left: 1.25rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.4375rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
            padding-left: 2rem;
        }

        .timeline-marker {
            position: absolute;
            left: -0.5rem;
            top: 0.3125rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
            z-index: 1;
        }

        .list-group-flush .list-group-item {
            border-left: 0;
            border-right: 0;
            border-radius: 0;
        }

        .badge {
            font-size: 0.75rem;
        }

        .accordion .card {
            border: 1px solid #e3e6f0;
            margin-bottom: 0.25rem;
        }

        .accordion .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0;
        }

        .accordion .btn-link {
            color: #5a5c69;
            text-decoration: none;
            padding: 0.75rem 1.25rem;
            border-radius: 0;
        }

        .accordion .btn-link:hover {
            color: #3a3b45;
            text-decoration: none;
        }

        .accordion .btn-link:focus {
            box-shadow: none;
        }

        .accordion .fa-chevron-right {
            transition: transform 0.2s ease-in-out;
        }

        .accordion .btn-link[aria-expanded="true"] .fa-chevron-right {
            transform: rotate(90deg);
        }

        .font-weight-bold {
            font-weight: 600 !important;
        }

        .rounded-circle {
            border-radius: 50% !important;
        }

        @media (max-width: 768px) {
            .timeline {
                padding-left: 1rem;
            }

            .timeline-item {
                padding-left: 1.5rem;
            }

            .timeline-marker {
                left: -0.375rem;
                width: 0.75rem;
                height: 0.75rem;
            }
        }
    </style>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // FAQ Accordion smooth toggle
            $('#faqAccordion .btn-link').on('click', function() {
                var target = $(this).attr('data-target');
                var chevron = $(this).find('.fa-chevron-right');

                // Reset all chevrons
                $('#faqAccordion .fa-chevron-right').css('transform', 'rotate(0deg)');

                // If this accordion is about to open, rotate chevron
                if (!$(target).hasClass('show')) {
                    chevron.css('transform', 'rotate(90deg)');
                }
            });

            // Handle chevron rotation on accordion events
            $('#faqAccordion .collapse').on('shown.bs.collapse', function() {
                $(this).prev().find('.fa-chevron-right').css('transform', 'rotate(90deg)');
            }).on('hidden.bs.collapse', function() {
                $(this).prev().find('.fa-chevron-right').css('transform', 'rotate(0deg)');
            });
        });
    </script>
@endsection
