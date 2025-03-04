<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaTerjadwal extends Model
{
    use HasFactory;
    protected $table = 'biaya_terjadwals';
    protected $primaryKey = 'id_biaya_terjadwal';
    protected $fillable = [
        'nama_biaya',
        'periode',
        'nominal',
    ];

    public function tagihanTerjadwal()
    {
        return $this->hasMany(TagihanTerjadwal::class, 'biaya_terjadwal_id', 'id_biaya_terjadwal');
    }

}
