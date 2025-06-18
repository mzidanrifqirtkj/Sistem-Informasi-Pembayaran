<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    protected $table = 'pembayarans';
    protected $primaryKey = 'id_pembayaran';
    protected $fillable = [
        'tagihan_bulanan_id',
        'tagihan_terjadwal_id',
        'nominal_pembayaran',
        'tanggal_pembayaran',
        'created_by_id',
        'payment_type', // Add this
        'total_allocations' // Add this
    ];

    // Add relationship
    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class, 'pembayaran_id', 'id_pembayaran');
    }

    // Add accessor
    public function getIsAllocatedAttribute()
    {
        return $this->payment_type === 'allocated';
    }

    public function tagihanBulanan()
    {
        return $this->belongsTo(TagihanBulanan::class, 'tagihan_bulanan_id', 'id_tagihan_bulanan');
    }

    public function tagihanTerjadwal()
    {
        return $this->belongsTo(TagihanTerjadwal::class, 'tagihan_terjadwal_id', 'id_tagihan_terjadwal');
    }


    public function santriTagihanBulanan()
    {
        return $this->hasOneThrough(
            Santri::class,
            TagihanBulanan::class,
            'id_tagihan_bulanan', // Foreign key di tagihan_bulanan_santri
            'id_santri',  // Foreign key di santri
            'tagihan_bulanan_id', // Foreign key di pembayaran
            'santri_id'  // Local key di tagihan_bulanan_santri
        );
    }
    public function santriTagihanTerjadwal()
    {
        return $this->hasOneThrough(
            Santri::class,
            TagihanTerjadwal::class,
            'id_tagihan_terjadwal', // Foreign key di tagihan_terjadwal_santri
            'id_santri',  // Foreign key di santri
            'tagihan_terjadwal_id', // Foreign key di pembayaran
            'santri_id'  // Local key di tagihan_bulanan_santri
        );
    }
}
