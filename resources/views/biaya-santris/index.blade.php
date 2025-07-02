@extends('layouts.home')
@section('title_page', 'Daftar Biaya Santri')

@section('content')
    <div class="container">
        @can('biaya-santri.create')
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('biaya-santris.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Paket Biaya
                    </a>
                </div>
            </div>
        @endcan

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Santri</th>
                                    <th>Jumlah Kategori</th>
                                    <th>Total Biaya</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($santris as $santri)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $santri->nama_santri }}</td>
                                        <td>{{ $santri->biayaSantris->count() }}</td>
                                        <td>Rp {{ number_format($santri->total_biaya, 0, ',', '.') }}</td>
                                        <td>
                                            @can('biaya-santri.view')
                                                <a href="{{ route('biaya-santris.show', $santri->id_santri) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            @endcan

                                            @can('biaya-santri.edit')
                                                <a href="{{ route('biaya-santris.edit', $santri->id_santri) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            @endcan

                                            @can('biaya-santri.delete')
                                                <form action="{{ route('biaya-santris.destroy', $santri->id_santri) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Hapus paket biaya ini?')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
