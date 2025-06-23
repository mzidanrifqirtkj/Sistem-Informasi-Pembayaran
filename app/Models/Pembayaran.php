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
        'total_allocations', // Add this
        'payment_note',     // string: catatan pembayaran
        'receipt_number',   // string: nomor kwitansi
        'is_void',         // boolean
        'voided_at',       // timestamp
        'voided_by',       // unsignedBigInteger
        'void_reason',     // text
    ];

    protected $casts = [
        'is_void' => 'boolean',
        'voided_at' => 'datetime',
        'tanggal_pembayaran' => 'datetime', // tambahkan ini
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

    // Tambahkan Relationship
    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by', 'id_user');
    }

    // Tambahkan Scopes
    public function scopeNotVoid($query)
    {
        return $query->where('is_void', false);
    }

    public function scopeVoid($query)
    {
        return $query->where('is_void', true);
    }

    // Tambahkan Accessors
    public function getCanVoidAttribute()
    {
        // Can void if not already void and within 24 hours
        return !$this->is_void &&
            $this->created_at->diffInHours(now()) <= 24;
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->is_void) {
            return '<span class="badge badge-danger">Void</span>';
        }

        return '<span class="badge badge-success">Success</span>';
    }

    // Tambahkan Method
    public function void($reason, $userId = null)
    {
        $this->update([
            'is_void' => true,
            'voided_at' => now(),
            'voided_by' => $userId ?? auth()->id_user(),
            'void_reason' => $reason
        ]);

        // Delete allocations if any
        if ($this->payment_type === 'allocated') {
            $this->paymentAllocations()->delete();
        }
    }

    // Update Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id_user');
    }

    public function getPaymentDescriptionAttribute()
    {
        // Refresh relationships
        $this->load(['paymentAllocations', 'tagihanBulanan', 'tagihanTerjadwal']);

        if ($this->payment_type === 'allocated') {
            $count = $this->paymentAllocations->count();
            return "Pembayaran untuk {$count} tagihan";
        }

        if ($this->tagihan_bulanan_id) {
            $tagihan = $this->tagihanBulanan;
            return "Syahriah {$tagihan->bulan} {$tagihan->tahun}";
        }

        if ($this->tagihan_terjadwal_id) {
            $tagihan = $this->tagihanTerjadwal;
            if ($tagihan && $tagihan->daftarBiaya && $tagihan->daftarBiaya->kategoriBiaya) {
                return $tagihan->daftarBiaya->kategoriBiaya->nama_kategori;
            }
            return 'Tagihan Terjadwal';
        }

        return 'Pembayaran';
    }

    public static function getTodayTotal()
    {
        return self::whereDate('tanggal_pembayaran', now()->toDateString())
            ->where('is_void', false)
            ->sum('nominal_pembayaran'); // pastikan nama kolom ini sesuai
    }

    public static function getMonthTotal()
    {
        return self::whereMonth('tanggal_pembayaran', now()->month)
            ->whereYear('tanggal_pembayaran', now()->year)
            ->where('is_void', false)
            ->sum('nominal_pembayaran'); // ganti jika nama kolom berbeda
    }

    public static function getYearTotal()
    {
        return self::whereYear('tanggal_pembayaran', now()->year)
            ->where('is_void', false)
            ->sum('nominal_pembayaran'); // sesuaikan jika field berbeda
    }

    // Accessor untuk mendapatkan NIS Santri
    public function getSantriNisAttribute()
    {
        // Refresh relationships untuk data terbaru
        $this->load(['tagihanBulanan.santri', 'tagihanTerjadwal.santri', 'paymentAllocations.tagihanBulanan.santri', 'paymentAllocations.tagihanTerjadwal.santri']);

        if ($this->tagihanBulanan && $this->tagihanBulanan->santri) {
            return $this->tagihanBulanan->santri->nis;
        }

        if ($this->tagihanTerjadwal && $this->tagihanTerjadwal->santri) {
            return $this->tagihanTerjadwal->santri->nis;
        }

        // Cek dari payment allocations
        $allocation = $this->paymentAllocations->first();
        if ($allocation) {
            if ($allocation->tagihanBulanan && $allocation->tagihanBulanan->santri) {
                return $allocation->tagihanBulanan->santri->nis;
            }
            if ($allocation->tagihanTerjadwal && $allocation->tagihanTerjadwal->santri) {
                return $allocation->tagihanTerjadwal->santri->nis;
            }
        }

        return '-';
    }

    // Accessor untuk mendapatkan Nama Santri
    public function getSantriNameAttribute()
    {
        // Refresh relationships untuk data terbaru
        $this->load(['tagihanBulanan.santri', 'tagihanTerjadwal.santri', 'paymentAllocations.tagihanBulanan.santri', 'paymentAllocations.tagihanTerjadwal.santri']);

        if ($this->tagihanBulanan && $this->tagihanBulanan->santri) {
            return $this->tagihanBulanan->santri->nama_santri;
        }

        if ($this->tagihanTerjadwal && $this->tagihanTerjadwal->santri) {
            return $this->tagihanTerjadwal->santri->nama_santri;
        }

        // Cek dari payment allocations
        $allocation = $this->paymentAllocations->first();
        if ($allocation) {
            if ($allocation->tagihanBulanan && $allocation->tagihanBulanan->santri) {
                return $allocation->tagihanBulanan->santri->nama_santri;
            }
            if ($allocation->tagihanTerjadwal && $allocation->tagihanTerjadwal->santri) {
                return $allocation->tagihanTerjadwal->santri->nama_santri;
            }
        }

        return '-';
    }

    // Accessor untuk format nominal
    public function getFormattedNominalAttribute()
    {
        // Pastikan menggunakan data terbaru
        $this->refresh();
        return format_rupiah($this->nominal_pembayaran);
    }

    // Update method getSantriAttribute untuk relationship yang lebih efisien
    public function getSantriAttribute()
    {
        if ($this->tagihanBulanan && $this->tagihanBulanan->santri) {
            return $this->tagihanBulanan->santri;
        }

        if ($this->tagihanTerjadwal && $this->tagihanTerjadwal->santri) {
            return $this->tagihanTerjadwal->santri;
        }

        $allocation = $this->paymentAllocations->first();
        if ($allocation) {
            if ($allocation->tagihanBulanan && $allocation->tagihanBulanan->santri) {
                return $allocation->tagihanBulanan->santri;
            }
            if ($allocation->tagihanTerjadwal && $allocation->tagihanTerjadwal->santri) {
                return $allocation->tagihanTerjadwal->santri;
            }
        }

        return null;
    }
}
