<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TambahanPembayaran extends Model
{
    use HasFactory;
    protected $table = 'tambahan_pembayarans';
    protected $primaryKey = 'id_tambahan_pembayaran';
    public $timestamps = false;

    protected $fillable = [
        'nama_item',
        'nominal',
        'jumlah'
    ];

    public function santriTambahanPembayarans()
    {
        return $this->hasMany(SantriTambahanPembayaran::class, 'tambahan_pembayaran_id', 'id_tambahan_pembayaran');
    }

    public function santris()
    {
        return $this->belongsToMany(Santri::class, 'santri_tambahan_pembayaran', 'tambahan_pembayaran_id', 'santri_id')
                    ->withTimestamps();
    }


}
