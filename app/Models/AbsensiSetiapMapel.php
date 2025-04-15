<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiSetiapMapel extends Model
{
    protected $table = 'absensi_mata_pelajaran';
    protected $fillable = [
        'absensi_harian_id',
        'mata_pelajaran_kelas_id',
        'santri_id',
        'status',
        'jam_mulai',
        'jam_selesai'
    ];

    public function absensiHarian()
    {
        return $this->belongsTo(AbsensiHarian::class);
    }

    public function mapelKelas()
    {
        return $this->belongsTo(MapelKelas::class);
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
