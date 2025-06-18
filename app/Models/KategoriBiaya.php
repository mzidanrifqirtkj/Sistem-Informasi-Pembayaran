<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBiaya extends Model
{
    use HasFactory;

    protected $table = 'kategori_biayas';
    protected $primaryKey = 'id_kategori_biaya';

    protected $fillable = [
        'nama_kategori',
        'status'
    ];

    public function daftarBiayas()
    {
        return $this->hasMany(DaftarBiaya::class, 'kategori_biaya_id', 'id_kategori_biaya');
    }
}
