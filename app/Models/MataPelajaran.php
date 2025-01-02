<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajarans';
    protected $primaryKey = 'id_mata_pelajaran';
    protected $fillable = ['nama_pelajaran', 'ustadz_id'];

    public function ustadz()
    {
        return $this->belongsTo(Santri::class, 'ustadz_id');
    }
}
