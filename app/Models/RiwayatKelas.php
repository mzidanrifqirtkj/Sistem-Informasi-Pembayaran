<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKelas extends Model
{
    protected $table = 'riwayat_kelas';
    protected $primaryKey = 'id_riwayat_kelas';
    protected $fillable = ['kelas_id', 'santri_id', 'tahun_ajar_id'];
    public $timestamps = true;

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }

    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class, 'tahun_ajar_id', 'id_tahun_ajar');
    }
}
