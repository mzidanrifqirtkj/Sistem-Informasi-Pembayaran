<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Santri extends Model
{
    use HasFactory;
    protected $guard = 'santri';
    protected $table = 'santris';
    protected $primaryKey = 'id_santri';
    public $timestamps = false;
    protected $fillable = [
        'nama_santri',
        'nis',
        'nik',
        'no_kk',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'no_hp',
        'alamat',
        'golongan_darah',
        'pendidikan_formal',
        'pendidikan_non_formal',
        'foto',
        'foto_kk',
        'tanggal_masuk',
        'is_ustadz',
        'user_id',
        'kategori_santri_id',
        'nama_ayah',
        'no_hp_ayah',
        'pekerjaan_ayah',
        'tempat_lahir_ayah',
        'tanggal_lahir_ayah',
        'alamat_ayah',
        'nama_ibu',
        'no_hp_ibu',
        'pekerjaan_ibu',
        'alamat_ibu',
        'tempat_lahir_ibu',
        'tanggal_lahir_ibu',
        'nama_wali',
        'no_hp_wali',
        'pekerjaan_wali',
        'alamat_wali',
        'tempat_lahir_wali',
        'tanggal_lahir_wali',
        'status',
        'tabungan'
    ];

    protected $casts = [
        'monthly_status' => 'array',
    ];

    // Verifikasi password untuk Santri berdasarkan user
    public function verifyPassword($password)
    {
        return $this->user->password === bcrypt($password);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function santriTambahanBulanans()
    {
        return $this->hasMany(SantriTambahanBulanan::class, 'santri_id', 'id_santri');
    }

    public function tambahanBulanans()
    {
        return $this->belongsToMany(TambahanBulanan::class, 'santri_tambahan_bulanans', 'santri_id', 'tambahan_bulanan_id')->withPivot(['jumlah'])->withTimestamps();
    }

    public function kategoriSantri()
    {
        return $this->belongsTo(KategoriSantri::class, 'kategori_santri_id', 'id_kategori_santri');
    }

    public function tagihanBulanan()
    {
        return $this->hasMany(TagihanBulanan::class, 'santri_id', 'id_santri');
    }


    public function tagihanTerjadwal()
    {
        return $this->hasMany(TagihanTerjadwal::class, 'santri_id', 'id_santri');
    }


    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'nis', 'nis');
    }

    public function penilaianSantri()
    {
        return $this->hasMany(PenilaianSantri::class, 'santri_id', 'id_santri');
    }

    public function qoriKelas()
    {
        return $this->hasMany(QoriKelas::class, 'nis', 'nis');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function biayaSantris()
    {
        return $this->hasMany(BiayaSantri::class, 'santri_id', 'id_santri');
    }

    public function kategoriBiayaJalur()
    {
        return $this->hasManyThrough(
            KategoriBiaya::class,
            DaftarBiaya::class,
            'id_daftar_biaya',       // Foreign key di DaftarBiaya yang terkait dengan BiayaSantri
            'id_kategori_biaya',     // Foreign key di KategoriBiaya
            'id_santri',             // Local key di Santri
            'daftar_biaya_id'        // Foreign key di BiayaSantri yang mengacu ke DaftarBiaya
        )->where('kategori_biayas.status', 'jalur');
    }

    /**
     * Get kelas aktif santri melalui riwayat kelas
     */
    public function getKelasAktifAttribute()
    {
        $riwayatTerbaru = $this->riwayatKelas()
            ->with('mapelKelas.kelas')
            ->latest()
            ->first();

        return $riwayatTerbaru?->mapelKelas?->kelas;
    }

    /**
     * Get nama kelas aktif
     */
    public function getNamaKelasAktifAttribute()
    {
        return $this->kelasAktif?->nama_kelas ?? 'Tanpa Kelas';
    }

    /**
     * Relationship ke riwayat kelas
     */
    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'santri_id', 'id_santri');
    }

    // public function absensiMataPelajaran()
    // {
    //     return $this->hasMany(AbsensiSetiapMapel::class);
    // }
}
