<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAllocation extends Model
{
    use HasFactory;

    protected $table = 'payment_allocations';

    protected $fillable = [
        'pembayaran_id',
        'tagihan_bulanan_id',
        'tagihan_terjadwal_id',
        'allocated_amount',
        'allocation_order'
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:0',
        'allocation_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id', 'id_pembayaran');
    }

    public function tagihanBulanan()
    {
        return $this->belongsTo(TagihanBulanan::class, 'tagihan_bulanan_id', 'id_tagihan_bulanan');
    }

    public function tagihanTerjadwal()
    {
        return $this->belongsTo(TagihanTerjadwal::class, 'tagihan_terjadwal_id', 'id_tagihan_terjadwal');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->allocated_amount, 0, ',', '.');
    }

    public function getTagihanTypeAttribute()
    {
        if ($this->tagihan_bulanan_id) {
            return 'bulanan';
        } elseif ($this->tagihan_terjadwal_id) {
            return 'terjadwal';
        }
        return null;
    }

    public function getTagihanDetailAttribute()
    {
        if ($this->tagihan_bulanan_id && $this->tagihanBulanan) {
            return $this->tagihanBulanan->bulan . ' ' . $this->tagihanBulanan->tahun;
        } elseif ($this->tagihan_terjadwal_id && $this->tagihanTerjadwal) {
            return $this->tagihanTerjadwal->daftarBiaya->nama_biaya ?? 'Tagihan Terjadwal';
        }
        return '-';
    }
}
