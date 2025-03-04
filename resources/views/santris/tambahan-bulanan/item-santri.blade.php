@extends('santris.layouts.home')
@section('title_page', 'Tambahan Santri')
@section('content')

    <div class="container">
        <h1 class="text-center mb-4">Tambahan Biaya Bulanan Santri</h1>
        <div class="table-responsive">
            <table id="example1" class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th scope="col" class="text-center">No</th>
                        <th scope="col">NIS</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Tambahan</th>
                        {{-- <th scope="col" class="text-center">Aksi</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($santris as $s)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $s->nis }}</td>
                            <td>{{ $s->nama_santri }}</td>
                            <td>{{ $s->kategoriSantri->nama_kategori }}</td>
                            <td>
                                @if ($s->tambahanBulanans->isNotEmpty())
                                    <ul class="list-group">
                                        @foreach ($s->tambahanBulanans as $item)
                                            <li class="list-group-item">
                                                <strong>{{ $item->nama_item }}</strong>
                                                <span class="text-muted">(Rp
                                                    {{ number_format($item->nominal, 0, ',', '.') }})</span>
                                                <br>
                                                <small>Jumlah: {{ $item->pivot->jumlah }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="badge bg-secondary">Tidak ada tambahan</span>
                                @endif
                            </td>
                            {{-- <td class="text-center">
                                <a href="{{ route('santri.tambahan_bulanan.item_santri.edit', $s) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
