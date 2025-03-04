<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriMapel extends Model
{
    use HasFactory;
    protected $table = 'kategori_mapels';
    protected $primaryKey = 'id_kategori_mapel';
    protected $fillable = [
        'nama_kategori_mapel',
    ];

    public function mataPelajaran()
    {
        return $this->hasMany(MataPelajaran::class, 'kategori_mapel_id', 'id_kategori_mapel');
    }
}
