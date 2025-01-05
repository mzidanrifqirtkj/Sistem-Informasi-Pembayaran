@extends('admin.layouts.app')
@section('content')
    <h1>Data Santri</h1>
    <table border="1">
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama Santri</th>
                <th>Alamat</th>
                <th>No HP</th>
                <th>Asal Sekolah</th>
                <th>Akun</th>
                <th>Kategori</th>
                <th>Nama Ayah</th>
                {{-- <th>NIK Ayah</th> --}}
                <th>Nama Ibu</th>
                {{-- <th>NIK Ibu</th> --}}
                <th>No HP Ayah</th>
                <th>No HP Ibu</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($santris as $santri)
                <tr>
                    <td>{{ $santri->nis }}</td>
                    <td>{{ $santri->nama_santri }}</td>
                    <td>{{ $santri->alamat }}</td>
                    <td>{{ $santri->no_hp }}</td>
                    <td>{{ $santri->pendidikan_formal }}</td>
                    <td>{{ $santri->user->email }}</td>
                    <td>{{ $santri->kategori_santri->nama_kategori }}</td>
                    <td>{{ $santri->nama_ayah ?? 'Tidak Ada' }}</td>
                    {{-- <td>{{ $santri->nik_ayah ?? 'Tidak Ada' }}</td> --}}
                    <td>{{ $santri->nama_ibu ?? 'Tidak Ada' }}</td>
                    {{-- <td>{{ $santri->nik_ibu ?? 'Tidak Ada' }}</td> --}}
                    <td>{{ $santri->no_hp_ayah ?? 'Tidak Ada' }}</td>
                    <td>{{ $santri->no_hp_ibu ?? 'Tidak Ada' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
