@extends('layouts.home')
@section('title_page', 'Buat Tagihan Individual')

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
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Buat Tagihan Individual</h2>
                    <a href="{{ route('tagihan_terjadwal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Form Section -->
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle"></i> Form Buat Tagihan
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tagihan_terjadwal.store') }}" method="POST" id="createTagihanForm">
                            @csrf

                            <div class="row">
                                <!-- Santri Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="santri_id" class="required">Pilih Santri</label>
                                        <select name="santri_id" id="santri_id"
                                            class="form-control @error('santri_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Santri --</option>
                                            @foreach ($santris as $santri)
                                                <option value="{{ $santri->id_santri }}"
                                                    {{ old('santri_id') == $santri->id_santri ? 'selected' : '' }}>
                                                    {{ $santri->nama_santri }} ({{ $santri->nis }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('santri_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tahun -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tahun" class="required">Tahun</label>
                                        <select name="tahun" id="tahun"
                                            class="form-control @error('tahun') is-invalid @enderror" required>
                                            <option value="">-- Pilih Tahun --</option>
                                            @for ($year = now()->year - 2; $year <= now()->year + 2; $year++)
                                                <option value="{{ $year }}"
                                                    {{ old('tahun', now()->year) == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('tahun')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Biaya Santri Selection -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="biaya_santri_id" class="required">Pilih Alokasi Biaya</label>
                                        <select name="biaya_santri_id" id="biaya_santri_id"
                                            class="form-control @error('biaya_santri_id') is-invalid @enderror" required
                                            disabled>
                                            <option value="">-- Pilih Santri Terlebih Dahulu --</option>
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle text-info"></i>
                                            Hanya menampilkan alokasi biaya dengan kategori <strong>Tahunan</strong> dan
                                            <strong>Insidental</strong>.
                                        </small>
                                        @error('biaya_santri_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden field for daftar_biaya_id -->
                            <input type="hidden" name="daftar_biaya_id" id="daftar_biaya_id"
                                value="{{ old('daftar_biaya_id') }}">

                            <div class="row">
                                <!-- Tahun Ajar -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tahun_ajar_id" class="required">Tahun Ajar</label>
                                        <select name="tahun_ajar_id" id="tahun_ajar_id"
                                            class="form-control @error('tahun_ajar_id') is-invalid @enderror">
                                            <option value="">-- Tidak Ada --</option>
                                            @foreach ($tahunAjars as $tahunAjar)
                                                <option value="{{ $tahunAjar->id_tahun_ajar }}"
                                                    {{ old('tahun_ajar_id') == $tahunAjar->id_tahun_ajar ? 'selected' : '' }}>
                                                    {{ $tahunAjar->tahun_ajar }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tahun_ajar_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nominal -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nominal" class="required">Nominal Tagihan</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number" name="nominal" id="nominal"
                                                class="form-control @error('nominal') is-invalid @enderror" placeholder="0"
                                                min="0" step="1000" required value="{{ old('nominal') }}">
                                        </div>
                                        <small class="form-text text-muted">
                                            Nominal akan otomatis terisi berdasarkan alokasi biaya yang dipilih.
                                        </small>
                                        @error('nominal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-group mt-4">
                                <div class="d-flex justify-content-between">
                                    <button type="button" onclick="window.history.back()" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save"></i> Simpan Tagihan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 mb-0">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Handle santri selection change
            $('#santri_id').on('change', function() {
                const santriId = $(this).val();
                const biayaSantriSelect = $('#biaya_santri_id');

                if (santriId) {
                    // Show loading
                    $('#loadingModal').modal('show');
                    biayaSantriSelect.prop('disabled', true).html('<option value="">Memuat...</option>');

                    // Fetch biaya santri data
                    $.ajax({
                        url: '{{ route('tagihan_terjadwal.getBiayaSantriBySantriId') }}',
                        type: 'GET',
                        data: {
                            santri_id: santriId
                        },
                        success: function(data) {
                            biayaSantriSelect.empty().append(
                                '<option value="">-- Pilih Alokasi Biaya --</option>');

                            if (data.length > 0) {
                                $.each(data, function(index, item) {
                                    biayaSantriSelect.append(
                                        '<option value="' + item.id +
                                        '" data-daftar-biaya-id="' + item
                                        .daftar_biaya_id + '" data-nominal="' + item
                                        .nominal_tagihan_default + '">' +
                                        item.nama_biaya_paket +
                                        '</option>'
                                    );
                                });
                                biayaSantriSelect.prop('disabled', false);
                            } else {
                                biayaSantriSelect.append(
                                    '<option value="">Tidak ada alokasi biaya Tahunan/Insidental untuk santri ini</option>'
                                );
                            }
                        },
                        error: function(xhr) {
                            console.error('Error fetching biaya santri:', xhr);
                            biayaSantriSelect.empty().append(
                                '<option value="">Error memuat data</option>');

                            // Show error message
                            alert(
                                'Terjadi kesalahan saat memuat data alokasi biaya. Silakan coba lagi.'
                            );
                        },
                        complete: function() {
                            $('#loadingModal').modal('hide');
                        }
                    });
                } else {
                    biayaSantriSelect.prop('disabled', true).html(
                        '<option value="">-- Pilih Santri Terlebih Dahulu --</option>');
                    $('#nominal').val('');
                    $('#daftar_biaya_id').val('');
                }
            });

            // Handle biaya santri selection change
            $('#biaya_santri_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const nominal = selectedOption.data('nominal');
                const daftarBiayaId = selectedOption.data('daftar-biaya-id');

                if (nominal) {
                    $('#nominal').val(nominal);
                } else {
                    $('#nominal').val('');
                }

                if (daftarBiayaId) {
                    $('#daftar_biaya_id').val(daftarBiayaId);
                } else {
                    $('#daftar_biaya_id').val('');
                }
            });

            // Format number input
            $('#nominal').on('input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(value);
            });

            // Form validation before submit
            $('#createTagihanForm').on('submit', function(e) {
                const santriId = $('#santri_id').val();
                const biayaSantriId = $('#biaya_santri_id').val();
                const nominal = $('#nominal').val();
                const tahun = $('#tahun').val();

                if (!santriId || !biayaSantriId || !nominal || !tahun) {
                    e.preventDefault();
                    alert('Mohon lengkapi semua field yang wajib diisi.');
                    return false;
                }

                if (parseFloat(nominal) < 0) {
                    e.preventDefault();
                    alert('Nominal tagihan tidak boleh negatif.');
                    return false;
                }

                // Disable submit button to prevent double submission
                $('#submitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            });

            // Restore old values if validation fails
            @if (old('santri_id'))
                $('#santri_id').trigger('change');
            @endif
        });
    </script>
@endsection

@section('css_inline')
    <style>
        .required::after {
            content: ' *';
            color: red;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
        }

        .input-group-text {
            background-color: #e9ecef;
            border-color: #ced4da;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
@endsection
