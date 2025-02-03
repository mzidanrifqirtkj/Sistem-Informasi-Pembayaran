<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajarans';
    protected $primaryKey = 'id_mapel';
    protected $fillable = ['nama_mapel', 'kategori_mapel_id'];

    public function kategoriMapel()
    {
        return $this->belongsTo(KategoriMapel::class, 'kategori_mapel_id', 'id_kategori_mapel');
    }

    public function mapelKelas()
    {
        return $this->hasMany(MapelKelas::class, 'mapel_id', 'id_mapel');
    }
}
