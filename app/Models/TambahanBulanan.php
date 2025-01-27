<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TambahanBulanan extends Model
{
    use HasFactory;
    protected $table = 'tambahan_bulanans';
    protected $primaryKey = 'id_tambahan_bulanan';
    public $timestamps = false;

    protected $fillable = [
        'nama_item',
        'nominal',
    ];
    public function santris() {
        return $this->belongsToMany(Santri::class, 'santri_tambahan_bulanans', 'tambahan_bulanan_id', 'santri_id')
            ->withPivot(['jumlah'])->withTimestamps();
    }

}
