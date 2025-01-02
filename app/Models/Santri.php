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
        'gologan_darah',
        'pendidikan_formal',
        'pendidikan_non_formal',
        'foto',
        'tanggal_masuk',
        'is_ustadz',
        'user_id',
        'paket_pembayaran_id',
        'nama_ayah',
        'no_hp_ayah',
        'pekerjaan_ayah',
        'tempat_lahir_ayah',
        'tahun_lahir_ayah',
        'alamat_ayah',
        'nama_ibu',
        'no_hp_ibu',
        'pekerjaan_ibu',
        'alamat_ibu',
        'tempat_lahir_ibu',
        'tahun_lahir_ibu',
        'nama_wali',
        'no_hp_wali',
        'pekerjaan_wali',
        'alamat_wali',
        'tempat_lahir_wali',
        'tahun_lahir_wali',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function paket_pembayaran()
    {
        return $this->belongsTo(PaketPembayaran::class, 'paket_pembayaran_id', 'id_paket_pembayaran');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }




}
