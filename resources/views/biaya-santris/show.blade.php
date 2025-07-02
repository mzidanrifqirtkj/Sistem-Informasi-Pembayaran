@extends('layouts.home')
@section('title_page', 'Detail Paket Biaya Santri')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Paket Biaya Santri: {{ $santri->nama_santri }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Nama Santri: {{ $santri->nama_santri }}</h5>
                            </div>
                            <div class="col-md-6 text-right">
                                <h5>Total Biaya: Rp {{ number_format($totalBiaya, 0, ',', '.') }}</h5>
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kategori Biaya</th>
                                    <th>Nominal</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($santri->biayaSantris as $biaya)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $biaya->daftarBiaya->kategoriBiaya->nama_kategori }}</td>
                                        <td>Rp {{ number_format($biaya->daftarBiaya->nominal, 0, ',', '.') }}</td>
                                        <td>{{ $biaya->jumlah }}</td>
                                        <td>Rp
                                            {{ number_format($biaya->daftarBiaya->nominal * $biaya->jumlah, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="text-right mt-3">
                            @if (!auth()->user()->hasRole('santri'))
                                <a href="{{ route('biaya-santris.edit', $santri->id_santri) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Paket
                                </a>
                                <form action="{{ route('biaya-santris.destroy', $santri->id_santri) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Hapus paket biaya ini?')">
                                        <i class="fas fa-trash"></i> Hapus Paket
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('biaya-santris.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
