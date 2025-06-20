@extends('layouts.home')
@section('title_page', 'Tambah Data Santri')
@section('content')

    <form action="{{ route('santri.update', $santri) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nama_santri">Nama Santri</label>
                        <input type="text" class="form-control @error('nama_santri') is-invalid @enderror"
                            name="nama_santri" value="{{ old('nama_santri', $santri->nama_santri) }}">
                        @error('nama_santri')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nis">NIS</label>
                        <input type="text" class="form-control @error('nis') is-invalid @enderror" name="nis"
                            value="{{ old('nis', $santri->nis) }}">
                        @error('nis')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nik">NIK</label>
                        <input type="text" class="form-control @error('nik') is-invalid @enderror" name="nik"
                            value="{{ old('nik', $santri->nik) }}">
                        @error('nik')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="no_kk">No KK</label>
                        <input type="text" class="form-control @error('no_kk') is-invalid @enderror" name="no_kk"
                            value="{{ old('no_kk', $santri->no_kk) }}">
                        @error('no_kk')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror"
                            name="tempat_lahir" value="{{ old('tempat_lahir', $santri->tempat_lahir) }}">
                        @error('tempat_lahir')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                            name="tanggal_lahir" value="{{ old('tanggal_lahir', $santri->tanggal_lahir) }}">
                        @error('tanggal_lahir')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>


            </div>

            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="no_hp">No. HP Santri</label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" name="no_hp"
                            value="{{ old('no_hp', $santri->no_hp) }}">
                        @error('no_hp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-sm">
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin">
                            <option value="Laki-laki"
                                {{ old('jenis_kelamin', $santri->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                Laki-laki</option>
                            <option value="Perempuan"
                                {{ old('jenis_kelamin', $santri->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="golongan_darah">Golongan Darah</label>
                        <input type="text" class="form-control @error('golongan_darah') is-invalid @enderror"
                            name="golongan_darah" value="{{ old('golongan_darah', $santri->golongan_darah) }}">
                        @error('golongan_darah')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="pendidikan_formal">Pendidikan Formal</label>
                        <input type="text" class="form-control @error('pendidikan_formal') is-invalid @enderror"
                            name="pendidikan_formal" value="{{ old('pendidikan_formal', $santri->pendidikan_formal) }}">
                        @error('pendidikan_formal')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="pendidikan_non_formal">Pendidikan Non Formal</label>
                        <input type="text" class="form-control @error('pendidikan_non_formal') is-invalid @enderror"
                            name="pendidikan_non_formal"
                            value="{{ old('pendidikan_non_formal', $santri->pendidikan_non_formal) }}">
                        @error('pendidikan_non_formal')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="tanggal_masuk">Tanggal Masuk</label>
                        <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                            name="tanggal_masuk" value="{{ old('tanggal_masuk', $santri->tanggal_masuk) }}">
                        @error('tanggal_masuk')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="alamat">Alamat Santri</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat">{{ old('alamat', $santri->alamat) }}</textarea>
                        @error('alamat')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="foto">Foto Santri</label>
                        <input type="file" class="form-control-file @error('foto') is-invalid @enderror"
                            name="foto" value="{{ old('foto', $santri->foto) }}">
                        <span class="text-small text-danger font-italic">File extension only: jpg, jpeg, png | Max Upload
                            Image is 2048 Kb</span>
                        @error('foto')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="foto_kk">Foto KK</label>
                        <input type="file" class="form-control-file @error('foto_kk') is-invalid @enderror"
                            name="foto_kk" value="{{ old('foto_kk', $santri->foto_kk) }}">
                        <span class="text-small text-danger font-italic">File extension only: jpg, jpeg, png | Max Upload
                            Image is 2048 Kb</span>
                        @error('foto_kk')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="is_ustadz">Apakah Santri Ustadz?</label>
                        <select class="form-control @error('is_ustadz') is-invalid @enderror" name="is_ustadz">
                            <option value="1" {{ old('is_ustadz', $santri->is_ustadz) == '1' ? 'selected' : '' }}>Ya
                            </option>
                            <option value="0" {{ old('is_ustadz', $santri->is_ustadz) == '0' ? 'selected' : '' }}>
                                Tidak</option>
                        </select>
                        @error('is_ustadz')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="user_email">User Email Santri</label>
                        <input type="text" class="form-control @error('user_email') is-invalid @enderror"
                            name="user_email" id="user_email"
                            value="{{ old('user_email', optional($santri->user)->email) }}" readonly>
                        @error('user_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-sm">
                    <div class="form-group">
                        <label for="kategori_santri_id">Kategori Santri</label>
                        <select class="form-control @error('kategori_santri_id') is-invalid @enderror"
                            name="kategori_santri_id" id="kategori_santri_id">
                            <option value="" disabled
                                {{ old('kategori_santri_id', $santri->kategori_santri_id) ? '' : 'selected' }}>
                                Pilih Kategori
                            </option>
                            @foreach ($kategori_santris as $kategori)
                                <option value="{{ $kategori->id_kategori_biaya }}"
                                    {{ old('kategori_santri_id', $santri->kategori_santri_id) == $kategori->id_kategori_biaya ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_santri_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-sm">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control @error('status') is-invalid @enderror" name="status">
                            <option value="" disabled
                                {{ old('status', $santri->status ?? '') == '' ? 'selected' : '' }}>Pilih Status</option>
                            <option value="aktif"
                                {{ old('status', $santri->status ?? '') == 'aktif' ? 'selected' : '' }}>aktif</option>
                            <option value="non_aktif"
                                {{ old('status', $santri->status ?? '') == 'non_aktif' ? 'selected' : '' }}>non_aktif
                            </option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- data orang tua --}}
            <h4>Data Ayah</h4>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nama_wali">Nama Ayah</label>
                        <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror"
                            name="nama_ayah" value="{{ old('nama_ayah', $santri->nama_ayah) }}">
                        @error('nama_ayah')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="no_hp_ayah">No. HP Ayah</label>
                        <input type="text" class="form-control @error('no_hp_ayah') is-invalid @enderror"
                            name="no_hp_ayah" value="{{ old('no_hp_ayah', $santri->no_hp_ayah) }}">
                        @error('no_hp_ayah')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
                        <input type="text" class="form-control @error('pekerjaan_ayah') is-invalid @enderror"
                            name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $santri->pekerjaan_ayah) }}">
                        @error('pekerjaan_ayah')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="alamat_ayah">Alamat Ayah</label>
                        <textarea class="form-control @error('alamat_ayah') is-invalid @enderror" name="alamat_ayah">{{ old('alamat_ayah', $santri->alamat_ayah) }}</textarea>
                        @error('alamat_ayah')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="tempat_lahir_ayah">Tempat Lahir Ayah</label>
                        <input type="text" class="form-control @error('tempat_lahir_ayah') is-invalid @enderror"
                            name="tempat_lahir_ayah" value="{{ old('tempat_lahir_ayah', $santri->tempat_lahir_ayah) }}">
                        @error('tempat_lahir_ayah')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="tanggal_lahir_ayah">Tanggal Lahir Ayah</label>
                        <input type="date" class="form-control @error('tanggal_lahir_ayah') is-invalid @enderror"
                            name="tanggal_lahir_ayah"
                            value="{{ old('tanggal_lahir_ayah', $santri->tanggal_lahir_ayah) }}">
                        @error('tanggal_lahir_ayah')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>


            </div>

            <h4>Data Ibu</h4>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nama_ibu">Nama Ibu</label>
                        <input type="text" class="form-control @error('nama_ibu') is-invalid @enderror"
                            name="nama_ibu" value="{{ old('nama_ibu', $santri->nama_ibu) }}">
                        @error('nama_ibu')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="no_hp_ibu">No. HP Ibu</label>
                        <input type="text" class="form-control @error('no_hp_ibu') is-invalid @enderror"
                            name="no_hp_ibu" value="{{ old('no_hp_ibu', $santri->no_hp_ibu) }}">
                        @error('no_hp_ibu')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
                        <input type="text" class="form-control @error('pekerjaan_ibu') is-invalid @enderror"
                            name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $santri->pekerjaan_ibu) }}">
                        @error('pekerjaan_ibu')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="alamat_ibu">Alamat ibu</label>
                        <textarea class="form-control @error('alamat_ibu') is-invalid @enderror" name="alamat_ibu">{{ old('alamat_ibu', $santri->alamat_ibu) }}</textarea>
                        @error('alamat_ibu')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="tempat_lahir_ibu">Tempat Lahir ibu</label>
                        <input type="text" class="form-control @error('tempat_lahir_ibu') is-invalid @enderror"
                            name="tempat_lahir_ibu" value="{{ old('tempat_lahir_ibu', $santri->tempat_lahir_ibu) }}">
                        @error('tempat_lahir_ibu')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="tanggal_lahir_ibu">Tanggal Lahir Ibu</label>
                        <input type="date" class="form-control @error('tanggal_lahir_ibu') is-invalid @enderror"
                            name="tanggal_lahir_ibu" value="{{ old('tanggal_lahir_ibu', $santri->tanggal_lahir_ibu) }}">
                        @error('tanggal_lahir_ibu')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>


            </div>

            <h4>Data Wali (Jika Ada)</h4>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nama_wali">Nama Wali</label>
                        <input type="text" class="form-control @error('nama_wali') is-invalid @enderror"
                            name="nama_wali" value="{{ old('nama_wali', $santri->nama_wali) }}">
                        @error('nama_wali')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="no_hp_wali">No. HP Wali</label>
                        <input type="text" class="form-control @error('no_hp_wali') is-invalid @enderror"
                            name="no_hp_wali" value="{{ old('no_hp_wali', $santri->no_hp_wali) }}">
                        @error('no_hp_wali')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="pekerjaan_wali">Pekerjaan Wali</label>
                        <input type="text" class="form-control @error('pekerjaan_wali') is-invalid @enderror"
                            name="pekerjaan_wali" value="{{ old('pekerjaan_wali', $santri->pekerjaan_wali) }}">
                        @error('pekerjaan_wali')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="alamat_wali">Alamat Wali</label>
                        <textarea class="form-control @error('alamat_wali') is-invalid @enderror" name="alamat_wali">{{ old('alamat_wali', $santri->alamat_wali) }}</textarea>
                        @error('alamat_wali')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="tempat_lahir_wali">Tempat Lahir wali</label>
                        <input type="text" class="form-control @error('tempat_lahir_wali') is-invalid @enderror"
                            name="tempat_lahir_wali" value="{{ old('tempat_lahir_wali', $santri->tempat_lahir_wali) }}">
                        @error('tempat_lahir_wali')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="tanggal_lahir_wali">Tanggal Lahir wali</label>
                        <input type="date" class="form-control @error('tanggal_lahir_wali') is-invalid @enderror"
                            name="tanggal_lahir_wali"
                            value="{{ old('tanggal_lahir_wali', $santri->tanggal_lahir_wali) }}">
                        @error('tanggal_lahir_wali')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>


            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-primary">Edit</button>
            <a href="{{ route('santri.show', $santri) }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>

@endsection
