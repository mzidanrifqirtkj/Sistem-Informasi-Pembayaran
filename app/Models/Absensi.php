<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    protected $fillable = [
        'riwayat_kelas_id',
        'santri_id',
        'tanggal_absen',
        'jumlah_hadir',
        'jumlah_izin',
        'jumlah_sakit',
        'jumlah_alpha',
        'keterangan'
    ];
    
    public $timestamps = false;

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }

    public function mata_pelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id', 'id_mata_pelajaran');
    }

    public function riwayat_kelas()
    {
        return $this->belongsTo(RiwayatKelas::class, 'riwayat_kelas_id', 'id_riwayat_kelas');
    }

    public function scopeBySantri($query, $santri_id)
    {
        return $query->where('santri_id', $santri_id);
    }



}
