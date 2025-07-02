<?php

namespace App\Models;

use DB;
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
        'sisa_pembayaran', // ADD THIS
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
        'sisa_pembayaran' => 'decimal:2', // ADD THIS
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

    // Update Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id_user');
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

    // ADD: Receipt details accessor
    public function getReceiptDetailsAttribute()
    {
        return [
            'total_received' => $this->nominal_pembayaran,
            'total_allocated' => $this->calculateTotalAllocated(),
            'overpayment' => $this->sisa_pembayaran ?? 0,
            'allocations' => $this->getAllocationDetails()
        ];
    }

    // ADD: Calculate total allocated
    public function calculateTotalAllocated()
    {
        if ($this->payment_type === 'allocated') {
            return $this->paymentAllocations->sum('allocated_amount');
        }

        // Single payment - calculate actual allocation
        if ($this->tagihan_bulanan_id && $this->tagihanBulanan) {
            $sisaTagihan = max(0, $this->tagihanBulanan->nominal -
                ($this->tagihanBulanan->total_pembayaran - $this->nominal_pembayaran));
            return min($this->nominal_pembayaran, $sisaTagihan);
        }

        if ($this->tagihan_terjadwal_id && $this->tagihanTerjadwal) {
            $sisaTagihan = max(0, $this->tagihanTerjadwal->nominal -
                ($this->tagihanTerjadwal->total_pembayaran - $this->nominal_pembayaran));
            return min($this->nominal_pembayaran, $sisaTagihan);
        }

        return $this->nominal_pembayaran - ($this->sisa_pembayaran ?? 0);
    }

    // ADD: Get allocation details
    public function getAllocationDetails()
    {
        // Modern allocation system (pure allocation)
        if ($this->payment_type === 'allocated' && $this->paymentAllocations->count() > 0) {
            return $this->paymentAllocations->map(function ($allocation) {
                $detail = [
                    'type' => $allocation->tagihan_type,
                    'amount' => $allocation->allocated_amount,
                    'order' => $allocation->allocation_order
                ];

                // Get description based on type
                if ($allocation->tagihan_bulanan_id && $allocation->tagihanBulanan) {
                    $detail['description'] = "Syahriah {$allocation->tagihanBulanan->bulan} {$allocation->tagihanBulanan->tahun}";
                    $detail['tagihan_id'] = $allocation->tagihan_bulanan_id;
                    $detail['status_after'] = $this->calculateStatusAfterAllocation($allocation->tagihanBulanan, $allocation->allocated_amount);
                } elseif ($allocation->tagihan_terjadwal_id && $allocation->tagihanTerjadwal) {
                    $detail['description'] = $allocation->tagihanTerjadwal->daftarBiaya->kategoriBiaya->nama_kategori ?? 'Tagihan Terjadwal';
                    $detail['tagihan_id'] = $allocation->tagihan_terjadwal_id;
                    $detail['status_after'] = $this->calculateStatusAfterAllocation($allocation->tagihanTerjadwal, $allocation->allocated_amount);
                }

                return $detail;
            })->sortBy('order');
        }

        // Legacy system support (direct link) - untuk backward compatibility
        $legacyDetails = collect();

        if ($this->tagihan_bulanan_id && $this->tagihanBulanan) {
            $legacyDetails->push([
                'type' => 'bulanan',
                'description' => "Syahriah {$this->tagihanBulanan->bulan} {$this->tagihanBulanan->tahun}",
                'amount' => $this->nominal_pembayaran,
                'tagihan_id' => $this->tagihan_bulanan_id,
                'order' => 1,
                'status_after' => $this->calculateStatusAfterAllocation($this->tagihanBulanan, $this->nominal_pembayaran)
            ]);
        }

        if ($this->tagihan_terjadwal_id && $this->tagihanTerjadwal) {
            $legacyDetails->push([
                'type' => 'terjadwal',
                'description' => $this->tagihanTerjadwal->daftarBiaya->kategoriBiaya->nama_kategori ?? 'Tagihan Terjadwal',
                'amount' => $this->nominal_pembayaran,
                'tagihan_id' => $this->tagihan_terjadwal_id,
                'order' => 1,
                'status_after' => $this->calculateStatusAfterAllocation($this->tagihanTerjadwal, $this->nominal_pembayaran)
            ]);
        }

        return $legacyDetails;
    }

    // ADD: Calculate status after allocation
    protected function calculateStatusAfterAllocation($tagihan, $allocationAmount)
    {
        $sisaSebelum = $tagihan->sisa_tagihan;
        $sisaSetelah = max(0, $sisaSebelum - $allocationAmount);

        if ($sisaSetelah == 0) {
            return 'lunas';
        } elseif ($sisaSetelah < $sisaSebelum) {
            return 'dibayar_sebagian';
        } else {
            return 'belum_lunas';
        }
    }

    // ADD: Get total allocated amount (vs total received)
    public function getTotalAllocatedAttribute()
    {
        if ($this->payment_type === 'allocated') {
            return $this->paymentAllocations->sum('allocated_amount');
        }

        // Legacy: assume full amount allocated
        return $this->nominal_pembayaran;
    }

    // ADD: Get overpayment amount
    public function getOverpaymentAttribute()
    {
        return $this->nominal_pembayaran - $this->total_allocated;
    }

    // ADD: Enhanced payment description
    public function getPaymentDescriptionAttribute()
    {
        $allocations = $this->getAllocationDetails();

        if ($allocations->count() > 1) {
            return "Pembayaran untuk {$allocations->count()} tagihan";
        } elseif ($allocations->count() == 1) {
            return $allocations->first()['description'];
        }

        return 'Pembayaran';
    }

    // ADD: Receipt summary data
    public function getReceiptDataAttribute()
    {
        return [
            'total_received' => $this->nominal_pembayaran,
            'total_allocated' => $this->total_allocated,
            'overpayment' => $this->overpayment,
            'allocations' => $this->getAllocationDetails(),
            'payment_date' => $this->tanggal_pembayaran,
            'receipt_number' => $this->receipt_number,
            'payment_note' => $this->payment_note,
            'created_by' => $this->createdBy->name ?? 'System'
        ];
    }

    // ADD: Check if payment is pure allocation
    public function getIsPureAllocationAttribute()
    {
        return $this->payment_type === 'allocated' &&
            is_null($this->tagihan_bulanan_id) &&
            is_null($this->tagihan_terjadwal_id);
    }

    // ADD: Validation method for allocation integrity
    public function validateAllocationIntegrity()
    {
        if ($this->payment_type === 'allocated') {
            $totalAllocated = $this->paymentAllocations->sum('allocated_amount');
            $overpayment = $this->overpayment;

            if (($totalAllocated + $overpayment) != $this->nominal_pembayaran) {
                throw new \Exception(
                    "Allocation integrity check failed: total_allocated ({$totalAllocated}) + overpayment ({$overpayment}) != nominal_pembayaran ({$this->nominal_pembayaran})"
                );
            }
        }

        return true;
    }

    // ENHANCED: Void method with allocation cleanup
    public function void($reason, $userId = null)
    {
        DB::beginTransaction();

        try {
            // Backup affected tagihan before void
            $affectedTagihan = collect();

            if ($this->payment_type === 'allocated') {
                foreach ($this->paymentAllocations as $allocation) {
                    if ($allocation->tagihan_bulanan_id) {
                        $affectedTagihan->push(['type' => 'bulanan', 'id' => $allocation->tagihan_bulanan_id]);
                    }
                    if ($allocation->tagihan_terjadwal_id) {
                        $affectedTagihan->push(['type' => 'terjadwal', 'id' => $allocation->tagihan_terjadwal_id]);
                    }
                }
            } else {
                // Legacy system
                if ($this->tagihan_bulanan_id) {
                    $affectedTagihan->push(['type' => 'bulanan', 'id' => $this->tagihan_bulanan_id]);
                }
                if ($this->tagihan_terjadwal_id) {
                    $affectedTagihan->push(['type' => 'terjadwal', 'id' => $this->tagihan_terjadwal_id]);
                }
            }

            // Void the payment
            $this->update([
                'is_void' => true,
                'voided_at' => now(),
                'voided_by' => $userId ?? auth()->id(),
                'void_reason' => $reason
            ]);

            // Delete allocations if any
            if ($this->payment_type === 'allocated') {
                $this->paymentAllocations()->delete();
            }

            // Update affected tagihan status
            $affectedTagihan->each(function ($tagihan) {
                try {
                    if ($tagihan['type'] === 'bulanan') {
                        $model = \App\Models\TagihanBulanan::find($tagihan['id']);
                        if ($model) {
                            $model->refresh();
                            $model->updateStatus();
                        }
                    } else {
                        $model = \App\Models\TagihanTerjadwal::find($tagihan['id']);
                        if ($model) {
                            $model->refresh();
                            $newStatus = $model->calculateStatus();
                            $model->update(['status' => $newStatus]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to update tagihan status after void', [
                        'type' => $tagihan['type'],
                        'id' => $tagihan['id'],
                        'error' => $e->getMessage()
                    ]);
                }
            });

            DB::commit();

            \Log::info('Payment voided successfully', [
                'pembayaran_id' => $this->id_pembayaran,
                'affected_tagihan' => $affectedTagihan->toArray(),
                'reason' => $reason
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function getTodayTotalForSantri($santriId)
    {
        return self::query()
            ->where(function ($query) use ($santriId) {
                $query->whereHas('tagihanBulanan', fn($q) => $q->where('santri_id', $santriId))
                    ->orWhereHas('tagihanTerjadwal', fn($q) => $q->where('santri_id', $santriId))
                    ->orWhereHas('paymentAllocations.tagihanBulanan', fn($q) => $q->where('santri_id', $santriId))
                    ->orWhereHas('paymentAllocations.tagihanTerjadwal', fn($q) => $q->where('santri_id', $santriId));
            })
            ->whereDate('tanggal_pembayaran', now())
            ->where('is_void', false)
            ->sum('nominal_pembayaran');
    }

    public static function getMonthTotalForSantri($santriId)
    {
        return self::query()
            ->where(function ($query) use ($santriId) {
                $query->whereHas('tagihanBulanan', fn($q) => $q->where('santri_id', $santriId))
                    ->orWhereHas('tagihanTerjadwal', fn($q) => $q->where('santri_id', $santriId))
                    ->orWhereHas('paymentAllocations.tagihanBulanan', fn($q) => $q->where('santri_id', $santriId))
                    ->orWhereHas('paymentAllocations.tagihanTerjadwal', fn($q) => $q->where('santri_id', $santriId));
            })
            ->whereMonth('tanggal_pembayaran', now()->month)
            ->whereYear('tanggal_pembayaran', now()->year)
            ->where('is_void', false)
            ->sum('nominal_pembayaran');
    }

    public static function getYearTotalForSantri($santriId)
    {
        return self::query()
            ->where(function ($query) use ($santriId) {
                $query->whereHas('tagihanBulanan', fn($q) => $q->where('santri_id', $santriId))
                    ->orWhereHas('tagihanTerjadwal', fn($q) => $q->where('santri_id', $santriId))
                    ->orWhereHas('paymentAllocations.tagihanBulanan', fn($q) => $q->where('santri_id', $santriId))
                    ->orWhereHas('paymentAllocations.tagihanTerjadwal', fn($q) => $q->where('santri_id', $santriId));
            })
            ->whereYear('tanggal_pembayaran', now()->year)
            ->where('is_void', false)
            ->sum('nominal_pembayaran');
    }
}


