<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanTahunan extends Model
{
    protected $table = 'tagihan_tahunan';
    protected $primaryKey = 'id_tagihan_tahunan';
    public $timestamps = false;

    protected $fillable = [
        'santri_id',
        'jenis_tagihan',
        'tahun',
        'jumlah',
        'status',
    ];
}
