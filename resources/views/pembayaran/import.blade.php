@extends('layouts.home')
@section('title_page', 'Import Pembayaran')

@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-8">
                <h1>Import Pembayaran dari Excel</h1>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('pembayaran.bulk.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Instructions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Petunjuk Import</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Format 1: Pembayaran Individual</h6>
                                <p>Untuk pembayaran dengan nominal berbeda per santri</p>
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>NIS</th>
                                            <th>Nominal</th>
                                            <th>Tanggal</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>001234</td>
                                            <td>500000</td>
                                            <td>{{ date('Y-m-d') }}</td>
                                            <td>Bayar SPP 3 bulan</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <a href="{{ route('pembayaran.bulk.template', ['type' => 'individual']) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i> Download Template
                                </a>
                            </div>

                            <div class="col-md-6">
                                <h6>Format 2: Pembayaran Bulk</h6>
                                <p>Untuk pembayaran nominal sama per bulan</p>
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Tahun</th>
                                            <th>Bulan</th>
                                            <th>Nominal</th>
                                            <th>List_NIS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ date('Y') }}</td>
                                            <td>Jan</td>
                                            <td>150000</td>
                                            <td>001234,001235,001236</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <a href="{{ route('pembayaran.bulk.template', ['type' => 'bulk']) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i> Download Template
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Upload File Excel</h5>
                    </div>
                    <div class="card-body">
                        <form id="importForm" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>Pilih Format Import <span class="text-danger">*</span></label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="import_type"
                                            id="individual_type" value="individual" checked>
                                        <label class="form-check-label" for="individual_type">
                                            Format Individual (Nominal Berbeda)
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="import_type" id="bulk_type"
                                            value="bulk">
                                        <label class="form-check-label" for="bulk_type">
                                            Format Bulk (Nominal Sama)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>File Excel <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file" name="file"
                                        accept=".xlsx,.xls" required>
                                    <label class="custom-file-label" for="file">Pilih file...</label>
                                </div>
                                <small class="form-text text-muted">
                                    Format yang didukung: .xlsx, .xls (Max: 5MB)
                                </small>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-primary" onclick="previewImport()">
                                    <i class="fas fa-eye"></i> Preview Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div id="previewSection" style="display: none;">
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Preview Data Import</h5>
                        </div>
                        <div class="card-body">
                            <div id="previewContent">
                                <!-- Preview content will be loaded here -->
                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-lg" onclick="processImport()">
                                    <i class="fas fa-check-circle"></i> Proses Import
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetImport()">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Modal -->
    <div class="modal fade" id="progressModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Processing Import...</h5>
                </div>
                <div class="modal-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: 100%"></div>
                    </div>
                    <p class="text-center mt-3 mb-0">Mohon tunggu, sedang memproses data...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Update file label
        $('.custom-file-input').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
        });

        function previewImport() {
            const form = document.getElementById('importForm');
            const formData = new FormData(form);

            // Validate file
            const fileInput = document.getElementById('file');
            if (!fileInput.files[0]) {
                Swal.fire('Error', 'Pilih file Excel terlebih dahulu', 'error');
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Loading...',
                text: 'Membaca file Excel...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send request
            $.ajax({
                url: '{{ route('pembayaran.bulk.import.preview') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        displayPreview(response.data);
                        $('#previewSection').show();
                    } else {
                        Swal.fire('Error', response.message || 'Gagal membaca file', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();

                    const message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                    Swal.fire('Error', message, 'error');
                }
            });
        }

        function displayPreview(data) {
            let html = '<div class="row mb-3">';
            html += '<div class="col-md-6">';
            html += `<div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Data Valid: <strong>${data.valid_count}</strong> baris
             </div>`;
            html += '</div>';
            html += '<div class="col-md-6">';
            html += `<div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                Data Error: <strong>${data.error_count}</strong> baris
             </div>`;
            html += '</div>';
            html += '</div>';

            // Valid data table
            if (data.valid_count > 0) {
                html += '<h6>Data Valid</h6>';
                html += '<div class="table-responsive mb-3">';
                html += '<table class="table table-sm table-bordered">';
                html += '<thead><tr><th>No</th><th>NIS</th><th>Nama</th><th>Nominal</th><th>Tanggal</th></tr></thead>';
                html += '<tbody>';

                data.valid.forEach((row, index) => {
                    html += `<tr>
                        <td>${index + 1}</td>
                        <td>${row.santri.nis}</td>
                        <td>${row.santri.nama_santri}</td>
                        <td>Rp ${new Intl.NumberFormat('id-ID').format(row.nominal)}</td>
                        <td>${row.tanggal}</td>
                     </tr>`;
                });

                html += '</tbody></table></div>';
            }

            // Error data table
            if (data.error_count > 0) {
                html += '<h6>Data Error</h6>';
                html += '<div class="table-responsive">';
                html += '<table class="table table-sm table-bordered table-danger">';
                html += '<thead><tr><th>Baris</th><th>NIS</th><th>Error</th></tr></thead>';
                html += '<tbody>';

                data.errors.forEach((error) => {
                    html += `<tr>
                        <td>${error.row}</td>
                        <td>${error.nis}</td>
                        <td>${error.error}</td>
                     </tr>`;
                });

                html += '</tbody></table></div>';
            }

            $('#previewContent').html(html);
        }

        function processImport() {
            Swal.fire({
                title: 'Konfirmasi Import',
                text: 'Proses import data pembayaran?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Proses',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#progressModal').modal('show');

                    $.ajax({
                        url: '{{ route('pembayaran.bulk.import.process') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#progressModal').modal('hide');

                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    if (response.report_url) {
                                        window.open(response.report_url, '_blank');
                                    }
                                    window.location.href = '{{ route('pembayaran.index') }}';
                                });
                            } else {
                                Swal.fire('Error', response.message || 'Import gagal', 'error');
                            }
                        },
                        error: function(xhr) {
                            $('#progressModal').modal('hide');

                            const message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                            Swal.fire('Error', message, 'error');
                        }
                    });
                }
            });
        }

        function resetImport() {
            $('#previewSection').hide();
            $('#previewContent').html('');
            $('#importForm')[0].reset();
            $('.custom-file-label').removeClass('selected').html('Pilih file...');
        }
    </script>
@endsection
