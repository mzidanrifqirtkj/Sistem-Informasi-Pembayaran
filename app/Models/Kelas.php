<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    protected $fillable = ['nama_kelas'];
    public $timestamps = true;

    // Relasi ke tabel absensis
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class, 'kelas_id', 'id_kelas');
    }

    // public function riwayatKelas()
    // {
    //     return $this->hasMany(RiwayatKelas::class, 'kelas_id', 'id_kelas');
    // }

    // public function mapelKelas()
    // {
    //     return $this->hasMany(MapelKelas::class, 'kelas_id', 'id_kelas');
    // }

    // public function waliKelas()
    // {
    //     return $this->hasOne(WaliKelas::class, 'kelas_id');
    // }


}
