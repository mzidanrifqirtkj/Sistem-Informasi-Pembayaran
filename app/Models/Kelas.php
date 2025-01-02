<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    protected $fillable = ['nama_kelas'];
    public $timestamps = true;

    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'kelas_id', 'id_kelas');
    }

}
