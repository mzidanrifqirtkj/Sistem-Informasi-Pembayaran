<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanBulanan extends Model
{
    use HasFactory;
    protected $table = 'tagihan_bulanans';
    protected $primaryKey = 'id_tagihan_bulanan';
    public $timestamps = false;
    protected $fillable = [
        'santri_id',
        'bulan',
        'tahun',
        'nominal',
        'rincian',
        'status'
    ];

    // Konversi otomatis kolom rincian ke array
    protected $casts = [
        'rincian' => 'array',
    ];

    // Method untuk menghitung total nominal dari rincian
    public function calculateNominal()
    {
        return collect($this->rincian)->sum('nominal');
    }

    // public function santri()
    // {
    //     return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    // }
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }


    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_bulanan_id', 'id_tagihan_bulanan');
    }

}
