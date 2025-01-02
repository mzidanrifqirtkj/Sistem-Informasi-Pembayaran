<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSantri extends Model
{
    /** @use HasFactory<\Database\Factories\KategoriSantriFactory> */
    use HasFactory;
    protected $table = 'kategori_santris';
    protected $primaryKey = 'id_kategori_santri';
    public $timestamps = false;
    protected $fillable = [
        'nama_kategori',
        'nominal_syahriyah',
    ];

    public function santri()
    {
        return $this->hasMany(Santri::class, 'kategori_santri_id', 'id_kategori_santri');
    }
}
