<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketPembayaran extends Model
{
    protected $table = 'paket_pembayarans';
    protected $primaryKey = 'id_paket_pembayaran';
    public $timestamps = false;

    protected $fillable = [
        'nama_paket',
        'nominal',
        'detail_pembayaran',
    ];

    public function santri(){
        return $this->hasMany(Santri::class);
    }
}
