<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanUstadz extends Model
{
    use HasFactory;

    protected $table = 'penugasan_ustadzs';
    protected $primaryKey = 'id_penugasan';
    protected $fillable = ['ustadz_id', 'kelas_id', 'tahun_ajar_id', 'mapel_id'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id', 'id_mapel');
    }

    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class, 'tahun_ajar_id');
    }

    public function ustadz()
    {
        return $this->belongsTo(Santri::class, 'ustadz_id', 'id_santri');
    }

    public function penilaianSantri()
    {
        return $this->hasMany(PenilaianSantri::class, 'penugasan_id', 'id_penugasan');
    }
}
