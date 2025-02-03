<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;
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
        return $this->hasMany(Absensi::class);
    }

    public function penilaianSantri()
    {
        return $this->hasMany(PenilaianSantri::class, 'santri_id', 'id_santri');
    }

    public function qoriKelas()
    {
        return $this->hasMany(QoriKelas::class, 'ustadz_id', 'id_santri');
    }
}
