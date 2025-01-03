<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<h1>Edit Santri</h1>

<form action="{{ route('santri.update', $santri->id_santri) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="nama_santri">Nama Santri</label>
        <input type="text" name="nama_santri" id="nama_santri" class="form-control" value="{{ old('nama_santri', $santri->nama_santri) }}" required>
    </div>

    <div class="form-group">
        <label for="nis">NIS</label>
        <input type="number" name="nis" id="nis" class="form-control" value="{{ old('nis', $santri->nis) }}" required>
    </div>

    <div class="form-group">
        <label for="nik">NIK</label>
        <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik', $santri->nik) }}" required>
    </div>
    <div class="form-group">
        <label for="nik">No. KK</label>
        <input type="text" name="no_kk" id="no_kk" class="form-control" value="{{ old('no_kk', $santri->no_kk) }}" required>
    </div>

    <div class="form-group">
        <label for="alamat">Alamat</label>
        <input type="text" name="alamat" id="alamat" class="form-control" value="{{ old('alamat', $santri->alamat) }}" required>
    </div>

    <div class="form-group">
        <label for="jenis_kelamin">Jenis Kelamin</label>
        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
            <option value="Laki-laki" {{ old('jenis_kelamin', $santri->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
            <option value="Perempuan" {{ old('jenis_kelamin', $santri->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div>

    <div class="form-group">
        <label for="tempat_lahir">Tempat Lahir</label>
        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $santri->tempat_lahir) }}" required>
    </div>

    <div class="form-group">
        <label for="tanggal_lahir">Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $santri->tanggal_lahir) }}" required>
    </div>

    <div class="form-group">
        <label for="no_hp">No. Hp</label>
        <input type="text" name="no_hp" id="no_hp" class="form-control" value="{{ old('no_hp', $santri->no_hp) }}" required>
    </div>



    <div class="form-group">
        <label for="golongan_darah">Golongan Darah</label>
        <select name="golongan_darah" id="golongan_darah" class="form-control" required>
            <option value="A" {{ old('golongan_darah', $santri->golongan_darah) == 'A' ? 'selected' : '' }}>A</option>
            <option value="B" {{ old('golongan_darah', $santri->golongan_darah) == 'B' ? 'selected' : '' }}>B</option>
            <option value="AB" {{ old('golongan_darah', $santri->golongan_darah) == 'AB' ? 'selected' : '' }}>AB</option>
            <option value="O" {{ old('golongan_darah', $santri->golongan_darah) == 'O' ? 'selected' : '' }}>O</option>
        </select>
    </div>
    <div class="form-group">
        <label for="pendidikan_formal">Pendidikan Formal Terakhir</label>
        <input type="text" name="pendidikan_formal" id="pendidikan_formal" class="form-control" value="{{ old('pendidikan_formal', $santri->pendidikan_formal) }}" required>
    </div>

    <div class="form-group">
        <label for="pendidikan_non_formal">Pendidikan Non Formal Terakhir</label>
        <input type="text" name="pendidikan_non_formal" id="pendidikan_non_formal" class="form-control" value="{{ old('pendidikan_non_formal', $santri->pendidikan_non_formal) }}" required>
    </div>

    <div class="form-group">
        <label for="tanggal_masuk">Tanggal Masuk</label>
        <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', $santri->tanggal_masuk) }}" required>
    </div>



    <div class="form-group">
        <label for="foto">Foto</label>
        <input type="file" name="foto" id="foto" class="form-control">
        @if ($santri->foto)
            <img src="{{ asset('storage/' . $santri->foto) }}" alt="Foto Santri" width="100">
        @endif
    </div>
    <div class="form-group">
        <label for="foto_kk">Foto KK</label>
        <input type="file" name="foto_kk" id="foto_kk" class="form-control">
        @if ($santri->foto_kk)
            <img src="{{ asset('storage/' . $santri->foto_kk) }}" alt="Foto KK" width="100">
        @endif
    </div>

    <div class="form-group">
        <label for="golongan_darah">Golongan Darah</label>
        <select name="golongan_darah" id="golongan_darah" class="form-control" required>
            <option value="A" {{ old('golongan_darah', $santri->golongan_darah) == 'A' ? 'selected' : '' }}>A</option>
            <option value="B" {{ old('golongan_darah', $santri->golongan_darah) == 'B' ? 'selected' : '' }}>B</option>
            <option value="AB" {{ old('golongan_darah', $santri->golongan_darah) == 'AB' ? 'selected' : '' }}>AB</option>
            <option value="O" {{ old('golongan_darah', $santri->golongan_darah) == 'O' ? 'selected' : '' }}>O</option>
        </select>
    </div>

    <div class="form-group">
        <label for="kategori_santri_id">Kategori Santri</label>
        <select name="kategori_santri_id" id="kategori_santri_id" class="form-control">
            <option value="" disabled {{ is_null(old('kategori_santri_id', $santri->kategori_santri_id)) ? 'selected' : '' }}>Please select</option>
            @foreach ($kategori_santris as $kategori)
                <option value="{{ $kategori->id_kategori }}" {{ old('kategori_santri_id', $santri->kategori_santri_id) == $kategori->id_kategori ? 'selected' : '' }}>
                    {{ $kategori->nama_kategori }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="is_ustadz">Ustadz</label>
        <select name="is_ustadz" id="is_ustadz" class="form-control" required>
            <option value="1" {{ old('is_ustadz', $santri->is_ustadz) == 1 ? 'selected' : '' }}>Ya</option>
            <option value="0" {{ old('is_ustadz', $santri->is_ustadz) == 0 ? 'selected' : '' }}>Tidak</option>
        </select>
    </div>

    <div class="form-group">
        <label for="nama_ayah">Nama Ayah</label>
        <input type="text" name="nama_ayah" id="nama_ayah" class="form-control" value="{{ old('nama_ayah', $santri->nama_ayah) }}" required>
    </div>
    <div class="form-group">
        <label for="no_hp_ayah">No. HP Ayah</label>
        <input type="text" name="no_hp_ayah" id="no_hp_ayah" class="form-control" value="{{ old('no_hp_ayah', $santri->no_hp_ayah) }}" required>
    </div>
    <div class="form-group">
        <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
        <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah', $santri->pekerjaan_ayah) }}" required>
    </div>
    <div class="form-group">
        <label for="tempat_lahir_ayah">Tempat Lahir Ayah</label>
        <input type="text" name="tempat_lahir_ayah" id="tempat_lahir_ayah" class="form-control" value="{{ old('tempat_lahir_ayah', $santri->tempat_lahir_ayah) }}" required>
    </div>
    <div class="form-group">
        <label for="tanggal_lahir_ayah">Tanggal Lahir Ayah</label>
        <input type="date" name="tanggal_lahir_ayah" id="tanggal_lahir_ayah" class="form-control" value="{{ old('tanggal_lahir_ayah', $santri->tanggal_lahir_ayah) }}" required>
    </div>

    <div class="form-group">
        <label for="alamat_ayah">Alamat Ayah</label>
        <input type="text" name="alamat_ayah" id="alamat_ayah" class="form-control" value="{{ old('alamat_ayah', $santri->alamat_ayah) }}" required>
    </div>

    {{-- Data Ibu --}}
    <div class="form-group">
        <label for="nama_ibu">Nama Ibu</label>
        <input type="text" name="nama_ibu" id="nama_ibu" class="form-control" value="{{ old('nama_ibu', $santri->nama_ibu) }}" required>
    </div>
    <div class="form-group">
        <label for="no_hp_ibu">No. HP Ibu</label>
        <input type="text" name="no_hp_ibu" id="no_hp_ibu" class="form-control" value="{{ old('no_hp_ibu', $santri->no_hp_ibu) }}" required>
    </div>
    <div class="form-group">
        <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
        <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu', $santri->pekerjaan_ibu) }}" required>
    </div>
    <div class="form-group">
        <label for="tempat_lahir_ibu">Tempat Lahir Ibu</label>
        <input type="text" name="tempat_lahir_ibu" id="tempat_lahir_ibu" class="form-control" value="{{ old('tempat_lahir_ibu', $santri->tempat_lahir_ibu) }}" required>
    </div>
    <div class="form-group">
        <label for="tanggal_lahir_ibu">Tanggal Lahir Ibu</label>
        <input type="date" name="tanggal_lahir_ibu" id="tanggal_lahir_ibu" class="form-control" value="{{ old('tanggal_lahir_ibu', $santri->tanggal_lahir_ibu) }}" required>
    </div>
    <div class="form-group">
        <label for="alamat_ibu">Alamat Ibu</label>
        <input type="text" name="alamat_ibu" id="alamat_ibu" class="form-control" value="{{ old('alamat_ibu', $santri->alamat_ibu) }}" required>
    </div>

    {{-- Data Wali --}}
    <div class="form-group">
        <label for="nama_wali">Nama Wali</label>
        <input type="text" name="nama_wali" id="nama_wali" class="form-control" value="{{ old('nama_wali', $santri->nama_wali) }}" required>
    </div>
    <div class="form-group">
        <label for="no_hp_wali">No. HP Wali</label>
        <input type="text" name="no_hp_wali" id="no_hp_wali" class="form-control" value="{{ old('no_hp_wali', $santri->no_hp_wali) }}" required>
    </div>
    <div class="form-group">
        <label for="pekerjaan_wali">Pekerjaan Wali</label>
        <input type="text" name="pekerjaan_wali" id="pekerjaan_wali" class="form-control" value="{{ old('pekerjaan_wali', $santri->pekerjaan_wali) }}" required>
    </div>
    <div class="form-group">
        <label for="tempat_lahir_wali">Tempat Lahir Wali</label>
        <input type="text" name="tempat_lahir_wali" id="tempat_lahir_wali" class="form-control" value="{{ old('tempat_lahir_wali', $santri->tempat_lahir_wali) }}" required>
    </div>
    <div class="form-group">
        <label for="tanggal_lahir_wali">Tanggal Lahir Wali</label>
        <input type="date" name="tanggal_lahir_wali" id="tanggal_lahir_wali" class="form-control" value="{{ old('tanggal_lahir_wali', $santri->tanggal_lahir_wali) }}" required>
    </div>
    <div class="form-group">
        <label for="alamat_wali">Alamat Wali</label>
        <input type="text" name="alamat_wali" id="alamat_wali" class="form-control" value="{{ old('alamat_wali', $santri->alamat_wali) }}" required>
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select name="status" id="status" class="form-control" required>
            <option value="Aktif" {{ old('status', $santri->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Nonaktif" {{ old('status', $santri->status) == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>

    <button type="submit" class="btn btn-success">Update</button>
</form>

{{-- Pesan flash dan error --}}
@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

</body>
</html>
