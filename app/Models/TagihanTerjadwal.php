<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanTerjadwal extends Model
{
    use HasFactory;
    protected $table = 'tagihan_terjadwals';
    protected $primaryKey = 'id_tagihan_terjadwal';
    public $timestamps = false;
    protected $fillable = [
        'nominal',
        'santri_id',
        'biaya_terjadwal_id',
        'status',
        'tahun',
        'semester',
        'rincian',
    ];

    protected $casts = [
        'rincian' => 'array',
    ];

    public function calculateNominal()
    {
        return collect($this->rincian)->sum('nominal');
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }


    public function biayaTerjadwal()
    {
        return $this->belongsTo(BiayaTerjadwal::class, 'biaya_terjadwal_id', 'id_biaya_terjadwal');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_tagihan_terjadwal', 'tagihan_terjadwal_id');
    }
    // public function pembayaran()
    // {
    //     return $this->hasMany(Pembayaran::class, 'id_tagihan_terjadwal', 'id_tagihan_terjadwal');
    // }
}
