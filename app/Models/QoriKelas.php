<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QoriKelas extends Model
{
    use HasFactory;

    protected $table = 'qori_kelas';
    protected $primaryKey = 'id_qori_kelas';
    protected $fillable = [
        'ustadz_id',
        'mapel_kelas_id',
    ];

    public function ustadz()
    {
        return $this->belongsTo(Santri::class, 'ustadz_id', 'id_santri');
    }

    public function mapelKelas()
    {
        return $this->belongsTo(MapelKelas::class, 'mapel_kelas_id', 'id_mapel_kelas');
    }

    public function penilaianSantri()
    {
        return $this->hasMany(PenilaianSantri::class, 'penugasan_id', 'id_penugasan');
    }
}
