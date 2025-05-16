<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QoriKelas extends Model
{
    use HasFactory;

    protected $table = 'qori_kelas';
    protected $primaryKey = 'id_qori_kelas';
    protected $fillable = [
        'santri_di',
        'mapel_kelas_id',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }

    public function mapelKelas()
    {
        return $this->hasMany(MapelKelas::class);
    }

    public function penilaianSantri()
    {
        return $this->hasMany(PenilaianSantri::class, 'penugasan_id', 'id_penugasan');
    }
}
