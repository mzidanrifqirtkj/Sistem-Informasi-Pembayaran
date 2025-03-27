<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiHarian extends Model
{
    use HasFactory;

    protected $table = 'absensi_harian';

    protected $primaryKey = 'id';
    protected $fillable = [
        'santri_id',
        'tanggal_hari',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }
}
