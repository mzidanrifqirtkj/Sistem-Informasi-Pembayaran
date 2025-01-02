<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'nilais';
    protected $primaryKey = 'id_nilai';
    protected $fillable = ['riwayat_kelas_id', 'mata_pelajaran_id', 'nilai_angka'];

    public function riwayatKelas()
    {
        return $this->belongsTo(RiwayatKelas::class, 'riwayat_kelas_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }
}
