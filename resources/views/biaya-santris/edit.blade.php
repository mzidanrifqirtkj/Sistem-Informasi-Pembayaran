<!-- resources/views/biaya-santris/edit.blade.php -->
@extends('layouts.home')
@section('title_page', 'Edit Paket Biaya Santri')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Paket Biaya Santri: {{ $santri->nama_santri }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('biaya-santris.update', $santri->id_santri) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="santri_id">Santri</label>
                                <select class="form-control" name="santri_id" id="santri_id" required>
                                    @foreach ($santris as $item)
                                        <option value="{{ $item->id_santri }}"
                                            {{ $item->id_santri == $santri->id_santri ? 'selected' : '' }}>
                                            {{ $item->nama_santri }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Biaya</label>
                                <div id="biaya-container">
                                    @foreach ($santri->biayaSantris as $biaya)
                                        <div class="biaya-item mb-3 p-3 border rounded">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <select class="form-control biaya-select"
                                                        name="biaya[{{ $loop->index }}][id]" required>
                                                        @foreach ($daftarBiayas as $item)
                                                            <option value="{{ $item->id_daftar_biaya }}"
                                                                data-nominal="{{ $item->nominal }}"
                                                                {{ $item->id_daftar_biaya == $biaya->daftar_biaya_id ? 'selected' : '' }}>
                                                                {{ $item->kategoriBiaya->nama_kategori }} - Rp
                                                                {{ number_format($item->nominal, 0, ',', '.') }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" name="biaya[{{ $loop->index }}][jumlah]"
                                                        class="form-control jumlah" value="{{ $biaya->jumlah }}"
                                                        min="1" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" class="btn btn-danger btn-sm remove-biaya">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" id="tambah-biaya" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Tambah Biaya
                                </button>
                            </div>

                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('biaya-santris.show', $santri->id_santri) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
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
            // Add new biaya item
            $('#tambah-biaya').click(function() {
                const index = Date.now();
                const newItem = `
                <div class="biaya-item mb-3 p-3 border rounded">
                    <div class="row">
                        <div class="col-md-5">
                            <select class="form-control biaya-select" name="biaya[${index}][id]" required>
                                <option value="">-- Pilih Biaya --</option>
                                @foreach ($daftarBiayas as $item)
                                    <option value="{{ $item->id_daftar_biaya }}" data-nominal="{{ $item->nominal }}">
                                        {{ $item->kategoriBiaya->nama_kategori }} - Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="biaya[${index}][jumlah]"
                                class="form-control jumlah"
                                value="1"
                                min="1" required>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-danger btn-sm remove-biaya">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            `;
                $('#biaya-container').append(newItem);
            });

            // Remove biaya item
            $(document).on('click', '.remove-biaya', function() {
                $(this).closest('.biaya-item').remove();
            });
        });
    </script>
@endsection
