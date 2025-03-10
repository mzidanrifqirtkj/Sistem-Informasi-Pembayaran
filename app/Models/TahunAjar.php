<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjar extends Model
{
    protected $table = 'tahun_ajars';
    protected $primaryKey = 'id_tahun_ajar';
    protected $fillable = [
        'tahun_ajar',
        'start_date',
        'end_date',
        'status'
    ];

    // Relasi ke tabel absensis
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class, 'tahun_ajar_id', 'id_tahun_ajar');
    }

    // public function mapelKelas()
    // {
    //     return $this->hasMany(MapelKelas::class, 'tahun_ajar_id', 'id_tahun_ajar');
    // }


    // public function penilaianSantri()
    // {
    //     return $this->hasMany(PenilaianSantri::class, 'tahun_ajar_id', 'id_tahun_ajar');
    // }

    // public function waliKelas()
    // {
    //     return $this->hasMany(WaliKelas::class, 'tahun_ajar_id', 'id_tahun_ajar');
    // }

}
