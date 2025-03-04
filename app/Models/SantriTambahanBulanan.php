<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SantriTambahanBulanan extends Model
{
    use HasFactory;
    protected $table = 'santri_tambahan_bulanans';
    protected $primaryKey = 'id_santri_tambahan_bulanan';
    public $timestamps = false;

    protected $fillable = [
        'santri_id',
        'tambahan_bulanan_id',
        'jumlah',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }

    public function tambahanBulanan()
    {
        return $this->belongsTo(TambahanBulanan::class, 'tambahan_bulanan_id', 'id_tambahan_bulanan');
    }

}
