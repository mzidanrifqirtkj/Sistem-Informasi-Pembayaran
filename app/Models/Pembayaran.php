<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayarans';
    protected $primaryKey = 'id_pembayaran';
    protected $fillable = ['tagihan_bulanan_id', 'tagihan_tahunan_id', 'tanggal_pembayaran', 'jumlah_dibayar'];

    public function tagihanBulanan()
    {
        return $this->belongsTo(TagihanBulanan::class, 'tagihan_bulanan_id');
    }

    public function tagihanTahunan()
    {
        return $this->belongsTo(TagihanTahunan::class, 'tagihan_tahunan_id');
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }

    
}
