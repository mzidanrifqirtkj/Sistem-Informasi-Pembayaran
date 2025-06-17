@extends('layouts.home')
@section('title_page', 'Edit Tagihan Terjadwal')

@section('content')
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Edit Tagihan Terjadwal</h2>
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

        <!-- Current Data Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle text-info"></i> Informasi Tagihan Saat Ini
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Santri:</small><br>
                                <strong>{{ $tagihanTerjadwal->santri->nama_santri ?? 'N/A' }}</strong>
                                <span class="badge badge-info ml-2">{{ $tagihanTerjadwal->santri->nis ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Jenis Biaya:</small><br>
                                <strong>{{ $tagihanTerjadwal->daftarBiaya->kategoriBiaya->nama_kategori ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Status Saat Ini:</small><br>
                                @php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($tagihanTerjadwal->status) {
                                        case 'belum_lunas':
                                            $statusClass = 'badge-danger';
                                            $statusText = 'Belum Lunas';
                                            break;
                                        case 'dibayar_sebagian':
                                            $statusClass = 'badge-warning';
                                            $statusText = 'Dibayar Sebagian';
                                            break;
                                        case 'lunas':
                                            $statusClass = 'badge-success';
                                            $statusText = 'Lunas';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                            $statusText = ucfirst($tagihanTerjadwal->status);
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit"></i> Form Edit Tagihan
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tagihan_terjadwal.update', $tagihanTerjadwal->id_tagihan_terjadwal) }}"
                            method="POST" id="editTagihanForm">
                            @csrf
                            @method('PUT')

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
                                                    {{ old('santri_id', $tagihanTerjadwal->santri_id) == $santri->id_santri ? 'selected' : '' }}>
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
                                                    {{ old('tahun', $tagihanTerjadwal->tahun) == $year ? 'selected' : '' }}>
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
                                            class="form-control @error('biaya_santri_id') is-invalid @enderror" required>
                                            <option value="">-- Memuat data... --</option>
                                            @foreach ($biayaSantrisUntukSantri as $biayaSantri)
                                                <option value="{{ $biayaSantri['id'] }}"
                                                    data-daftar-biaya-id="{{ $biayaSantri['daftar_biaya_id'] }}"
                                                    data-nominal="{{ $biayaSantri['nominal_tagihan_default'] }}"
                                                    {{ old('biaya_santri_id', $tagihanTerjadwal->biaya_santri_id) == $biayaSantri['id'] ? 'selected' : '' }}>
                                                    {{ $biayaSantri['nama_biaya_paket'] }}
                                                </option>
                                            @endforeach
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
                                value="{{ old('daftar_biaya_id', $tagihanTerjadwal->daftar_biaya_id) }}">

                            <div class="row">
                                <!-- Tahun Ajar -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tahun_ajar_id">Tahun Ajar (Opsional)</label>
                                        <select name="tahun_ajar_id" id="tahun_ajar_id"
                                            class="form-control @error('tahun_ajar_id') is-invalid @enderror">
                                            <option value="">-- Tidak Ada --</option>
                                            @foreach ($tahunAjars as $tahunAjar)
                                                <option value="{{ $tahunAjar->id_tahun_ajar }}"
                                                    {{ old('tahun_ajar_id', $tagihanTerjadwal->tahun_ajar_id) == $tahunAjar->id_tahun_ajar ? 'selected' : '' }}>
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
                                                class="form-control @error('nominal') is-invalid @enderror"
                                                placeholder="0" min="0" step="1000" required
                                                value="{{ old('nominal', $tagihanTerjadwal->nominal) }}">
                                        </div>
                                        <small class="form-text text-muted">
                                            Nominal dapat disesuaikan dari nominal default alokasi biaya.
                                        </small>
                                        @error('nominal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Status -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="status" class="required">Status Tagihan</label>
                                        <select name="status" id="status"
                                            class="form-control @error('status') is-invalid @enderror" required>
                                            <option value="belum_lunas"
                                                {{ old('status', $tagihanTerjadwal->status) == 'belum_lunas' ? 'selected' : '' }}>
                                                Belum Lunas
                                            </option>
                                            <option value="dibayar_sebagian"
                                                {{ old('status', $tagihanTerjadwal->status) == 'dibayar_sebagian' ? 'selected' : '' }}>
                                                Dibayar Sebagian
                                            </option>
                                            <option value="lunas"
                                                {{ old('status', $tagihanTerjadwal->status) == 'lunas' ? 'selected' : '' }}>
                                                Lunas
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle text-info"></i>
                                            Status ini akan otomatis diperbarui berdasarkan pembayaran yang masuk.
                                        </small>
                                        @error('status')
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
                                        <i class="fas fa-save"></i> Update Tagihan
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
                                    const isSelected = item.id ==
                                        '{{ old('biaya_santri_id', $tagihanTerjadwal->biaya_santri_id) }}';
                                    biayaSantriSelect.append(
                                        '<option value="' + item.id +
                                        '" data-daftar-biaya-id="' + item
                                        .daftar_biaya_id + '" data-nominal="' + item
                                        .nominal_tagihan_default + '"' + (
                                            isSelected ? ' selected' : '') + '>' +
                                        item.nama_biaya_paket +
                                        '</option>'
                                    );
                                });
                                biayaSantriSelect.prop('disabled', false);

                                // Trigger change to set daftar_biaya_id
                                biayaSantriSelect.trigger('change');
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
                    $('#daftar_biaya_id').val('');
                }
            });

            // Handle biaya santri selection change
            $('#biaya_santri_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const daftarBiayaId = selectedOption.data('daftar-biaya-id');

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
            $('#editTagihanForm').on('submit', function(e) {
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

                // Confirm if user really wants to update
                if (!confirm('Apakah Anda yakin ingin mengupdate tagihan ini?')) {
                    e.preventDefault();
                    return false;
                }

                // Disable submit button to prevent double submission
                $('#submitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            });

            // Initialize form if santri is already selected
            if ($('#santri_id').val()) {
                // Set initial daftar_biaya_id from selected biaya_santri
                const selectedBiayaSantri = $('#biaya_santri_id').find('option:selected');
                const daftarBiayaId = selectedBiayaSantri.data('daftar-biaya-id');
                if (daftarBiayaId) {
                    $('#daftar_biaya_id').val(daftarBiayaId);
                }
            }
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

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .badge {
            font-size: 0.8rem;
        }
    </style>
@endsection
