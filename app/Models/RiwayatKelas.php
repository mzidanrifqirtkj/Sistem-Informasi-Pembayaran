<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKelas extends Model
{
    protected $table = 'riwayat_kelas';
    protected $primaryKey = 'id_riwayat_kelas';
    protected $fillable = ['mapel_kelas_id', 'santri_id'];
    public $timestamps = true;

    public function mapelKelas()
    {
        return $this->belongsTo(MapelKelas::class, 'mapel_kelas_id', 'id_mapel_kelas');
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }

}
