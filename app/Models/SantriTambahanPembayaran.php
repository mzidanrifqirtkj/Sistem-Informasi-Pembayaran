<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SantriTambahanPembayaran extends Model
{
    use HasFactory;
    protected $table = 'santri_tambahan_pembayarans';
    protected $primaryKey = 'id_santri_tambahan_pembayaran';
    public $timestamps = false;

    protected $fillable = [
        'santri_id',
        'tambahan_pembayaran_id',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }

    public function tambahanPembayaran()
    {
        return $this->belongsTo(TambahanPembayaran::class, 'tambahan_pembayaran_id', 'id');
    }

}
