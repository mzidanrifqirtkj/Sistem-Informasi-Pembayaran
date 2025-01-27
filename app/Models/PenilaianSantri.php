<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianSantri extends Model
{
    use HasFactory;

    protected $table = 'penilaian_santri';
    protected $primaryKey = 'id_penilaian_santri';
    protected $fillable = [
        'santri_id',
        'penugasan_id',
        'nilai_tugas',
        'nilai_uh',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
        'semester',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }

    public function penugasan()
    {
        return $this->belongsTo(PenugasanUstadz::class, 'penugasan_id', 'id_penugasan');
    }
}
