<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapelKelas extends Model
{

    protected $table = 'mapel_kelas';
    protected $primaryKey = 'id_mapel_kelas';
    protected $fillable = [
        'kelas_id',
        'mapel_id',
        'tahun_ajar_id',
        'jam_mulai',
        'jam_selesai'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id', 'id_mapel');
    }

    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class, 'tahun_ajar_id', 'id_tahun_ajar');
    }

    public function qoriKelas()
    {
        return $this->belongsTo(QoriKelas::class, 'qori_id', 'id_qori_kelas');
    }

}
