{{-- @extends('layouts.home')
@section('title_page', 'Edit Item')
@section('content')

    <form action="{{ route('admin.tambahan_bulanan.update_item_santri', $santri) }}" method="post">
        @csrf
        @method('PUT')
        <div class="container">
            <div class="row mb-3">
                <div class="col">
                    <h4>Edit Tambahan Bulanan untuk: {{ $santri->nama }}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama Item</th>
                                <th scope="col">Aktif</th>
                                <th scope="col">Jumlah (Nominal)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($santri->tambahanBulanans as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_item }}</td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="items[{{ $item->id_tambahan_bulanan }}][aktif]"
                                            value="1"
                                            {{ $santri->tambahanBulanans->contains($item->id_tambahan_bulanan) ? 'checked' : '' }}
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            class="form-control"
                                            name="items[{{ $item->id_tambahan_bulanan }}][jumlah]"
                                            value="{{ old('items.' . $item->id_tambahan_bulanan . '.jumlah', $santri->tambahanBulanans->where('id_tambahan_bulanan', $item->id_tambahan_bulanan)->first()?->pivot->jumlah ?? 0) }}"
                                            min="0"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.tambahan_bulanan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection --}}
@extends('layouts.home')
@section('title_page', 'Edit Item Tambahan Bulanan')
@section('content')

    <form action="{{ route('admin.tambahan_bulanan.item_santri.update', $santri) }}" method="post">
        @csrf
        @method('PUT')
        <div class="container">
            <div class="row mb-3">
                <div class="col">
                    <h4>Edit Tambahan Bulanan untuk: {{ $santri->nama_santri }}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama Item</th>
                                <th scope="col">Aktif</th>
                                <th scope="col">Jumlah (Nominal)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @php
                                    // Cek apakah item sudah dimiliki santri
                                    $pivot = $santri->tambahanBulanans->where('id_tambahan_bulanan', $item->id_tambahan_bulanan)->first();
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_item }}</td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="items[{{ $item->id_tambahan_bulanan }}][aktif]"
                                            value="1"
                                            {{ $pivot ? 'checked' : '' }}
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            class="form-control"
                                            name="items[{{ $item->id_tambahan_bulanan }}][jumlah]"
                                            value="{{ old('items.' . $item->id_tambahan_bulanan . '.jumlah', $pivot->pivot->jumlah ?? 0) }}"
                                            min="0"
                                            {{ !$pivot ? 'disabled' : '' }}
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.tambahan_bulanan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        // Mengaktifkan/menonaktifkan input jumlah berdasarkan checkbox
        document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const jumlahInput = this.closest('tr').querySelector('input[type="number"]');
                if (this.checked) {
                    jumlahInput.removeAttribute('disabled');
                } else {
                    jumlahInput.setAttribute('disabled', 'disabled');
                    jumlahInput.value = 0; // Reset nilai jika dinonaktifkan
                }
            });
        });
    </script>

@endsection
