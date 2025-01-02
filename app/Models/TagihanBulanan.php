<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanBulanan extends Model
{
    protected $table = 'tagihan_bulanans';
    protected $primaryKey = 'id_tagihan_bulanan';
    protected $fillable = [
        'santri_id',
        'nama_tagihan',
        'bulan',
        'tahun',
        'jumlah',
        'status'
    ];


    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }
}
