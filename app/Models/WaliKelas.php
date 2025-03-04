<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaliKelas extends Model
{
    protected $table = 'wali_kelas';
    protected $primaryKey = 'id_wali_kelas';
    protected $fillable = ['ustadz_id', 'kelas_id', 'tahun_ajar_id'];

    public function ustadz()
    {
        return $this->belongsTo(Santri::class, 'ustadz_id', 'id_santri');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }

    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class, 'tahun_ajar_id', 'id_tahun_ajar');
    }
}
