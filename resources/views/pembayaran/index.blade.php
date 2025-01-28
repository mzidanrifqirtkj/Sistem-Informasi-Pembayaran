@extends('layouts.home')
@section('title_page','Pembayaran Santri')
@section('content')


<div class="container">
    <h1>Pilih Santri untuk Membayar Tagihan</h1>
    <table id="example1" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($santris as $s)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $s->nis }}</td>
                <td>{{ $s->nama_santri }}</td>
                <td>{{ $s->kategoriSantri->nama_kategori }}</td>
                <td>
                    <a href="{{ route('admin.pembayaran.show', $s->id_santri) }}" class="btn btn-primary">Lihat Tagihan</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
