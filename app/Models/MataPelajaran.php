<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajarans';
    protected $primaryKey = 'id_mapel';
    protected $fillable = ['nama_mapel'];

    public function penugasanUstadz()
    {
        return $this->hasMany(PenugasanUstadz::class, 'mapel_id', 'id_mapel');
    }
}
