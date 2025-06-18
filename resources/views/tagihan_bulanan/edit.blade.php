@extends('layouts.home')

@section('title_page', 'Edit Tagihan Bulanan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Edit Tagihan Bulanan</h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Info Tagihan -->
                        <div class="alert alert-info">
                            <h5>Informasi Tagihan</h5>
                            <p class="mb-1"><strong>Santri:</strong> {{ $tagihan->santri->nama_santri }}
                                ({{ $tagihan->santri->nis }})</p>
                            <p class="mb-1"><strong>Bulan/Tahun:</strong> {{ $tagihan->bulan }} {{ $tagihan->tahun }}</p>
                            <p class="mb-0"><strong>Status:</strong>
                                <span class="badge bg-{{ $tagihan->status_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $tagihan->status)) }}
                                </span>
                            </p>
                        </div>

                        <form method="POST" action="{{ route('tagihan_bulanan.update', $tagihan->id_tagihan_bulanan) }}"
                            id="editForm">
                            @csrf
                            @method('PUT')

                            <!-- Current Rincian -->
                            <div class="mb-3">
                                <h5>Rincian Biaya Saat Ini</h5>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Jenis Biaya</th>
                                            <th class="text-end">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tagihan->rincian as $rincian)
                                            <tr>
                                                <td>{{ $rincian['nama'] }}</td>
                                                <td class="text-end">Rp
                                                    {{ number_format($rincian['nominal'], 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-end">Rp
                                                {{ number_format($tagihan->calculateNominal(), 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Nominal -->
                            <div class="mb-3">
                                <label for="nominal" class="form-label required">Nominal Tagihan</label>
                                <input type="number" name="nominal" id="nominal" class="form-control"
                                    value="{{ old('nominal', $tagihan->nominal) }}" min="0" required>
                                <small class="form-text text-muted">
                                    Ubah nominal jika berbeda dari total rincian
                                </small>
                            </div>

                            <!-- Custom Rincian Option -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="useCustomRincian">
                                    <label class="form-check-label" for="useCustomRincian">
                                        Ubah rincian biaya
                                    </label>
                                </div>
                            </div>

                            <div id="customRincianSection" style="display: none;">
                                <h5>Rincian Custom</h5>
                                <div id="customRincianList"></div>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="addCustomRincian()">
                                    <i class="fas fa-plus"></i> Tambah Rincian
                                </button>
                                <hr>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Perhatian:</strong> Mengubah tagihan ini akan mempengaruhi laporan keuangan.
                                Pastikan perubahan sudah sesuai dan disetujui.
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tagihan_bulanan.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#useCustomRincian').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#customRincianSection').show();
                    // Load existing rincian
                    loadExistingRincian();
                } else {
                    $('#customRincianSection').hide();
                    $('#customRincianList').empty();
                }
            });

            // Confirm before submit
            $('#editForm').on('submit', function(e) {
                if (!confirm('Apakah Anda yakin ingin mengubah tagihan ini?')) {
                    e.preventDefault();
                }
            });
        });

        let customRincianIndex = 0;

        function loadExistingRincian() {
            const rincian = @json($tagihan->rincian);
            $('#customRincianList').empty();

            rincian.forEach(function(item) {
                addCustomRincian(item.nama, item.nominal);
            });
        }

        function addCustomRincian(nama = '', nominal = '') {
            const html = `
        <div class="row mb-2" id="rincian_${customRincianIndex}">
            <div class="col-md-6">
                <input type="text"
                       name="custom_rincian[${customRincianIndex}][nama]"
                       class="form-control form-control-sm"
                       placeholder="Nama biaya"
                       value="${nama}"
                       required>
            </div>
            <div class="col-md-4">
                <input type="number"
                       name="custom_rincian[${customRincianIndex}][nominal]"
                       class="form-control form-control-sm"
                       placeholder="Nominal"
                       value="${nominal}"
                       min="0"
                       required
                       onchange="calculateTotal()">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeCustomRincian(${customRincianIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
            $('#customRincianList').append(html);
            customRincianIndex++;
            calculateTotal();
        }

        function removeCustomRincian(index) {
            $(`#rincian_${index}`).remove();
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            $('input[name^="custom_rincian"][name$="[nominal]"]').each(function() {
                total += parseInt($(this).val()) || 0;
            });

            if (total > 0) {
                $('#nominal').val(total);
            }
        }
    </script>
@endsection
