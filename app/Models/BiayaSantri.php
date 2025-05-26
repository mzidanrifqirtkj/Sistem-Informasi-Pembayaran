<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaSantri extends Model
{
    use HasFactory;

    protected $table = 'biaya_santris';
    protected $primaryKey = 'id_biaya_santri';

    protected $fillable = ['santri_id', 'daftar_biaya_id', 'jumlah'];

    public function daftarBiaya()
    {
        return $this->belongsTo(DaftarBiaya::class, 'daftar_biaya_id', 'id_daftar_biaya');
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }
}
