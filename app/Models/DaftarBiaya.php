<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarBiaya extends Model
{
    use HasFactory;

    protected $table = 'daftar_biayas';
    protected $primaryKey = 'id_daftar_biaya';

    protected $fillable = ['kategori_biaya_id', 'nominal'];

    public function kategoriBiaya()
    {
        return $this->belongsTo(KategoriBiaya::class, 'kategori_biaya_id', 'id_kategori_biaya');
    }

    public function biayaSantris()
    {
        return $this->hasMany(BiayaSantri::class, 'daftar_biaya_id', 'id_daftar_biaya');
    }
}
