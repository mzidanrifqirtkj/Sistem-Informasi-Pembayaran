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
    ];

    public function santris()
    {
        return $this->belongsToMany(Santri::class, 'santri_tambahan_pembayarans', 'id_tambahan_pembayaran', 'id_santri')
                    ->withPivot('id_tambahan_pembayaran', 'id_santri') // Jika Anda ingin menyertakan data di pivot
                    ->withTimestamps(); // Jika Anda ingin menyertakan kolom timestamps (created_at, updated_at)
    }
}
