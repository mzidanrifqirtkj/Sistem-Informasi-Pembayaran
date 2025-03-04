<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianSantri extends Model
{
    use HasFactory;

    protected $table = 'penilaian_santri';
    protected $primaryKey = 'id_penilaian_santri';
    protected $fillable = ['santri_id', 'penugasan_ustadz_id', 'nilai', 'semester'];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }

    public function qoriKelas()
    {
        return $this->belongsTo(QoriKelas::class, 'penugasan_ustadz_id', 'id_penugasan_ustadz');
    }
}
