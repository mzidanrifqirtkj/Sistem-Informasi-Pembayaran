<?php

namespace App\Observers;

use App\Models\Pembayaran;
use App\Models\TagihanTerjadwal;
use App\Models\TagihanBulanan;
use App\Models\AuditLog;
use App\Services\PaymentAllocationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PembayaranObserver
{
    protected $paymentAllocationService;

    public function __construct(PaymentAllocationService $paymentAllocationService)
    {
        $this->paymentAllocationService = $paymentAllocationService;
    }

    public function creating(Pembayaran $pembayaran): bool
    {
        // Check for duplicate payment
        $recentPayment = Pembayaran::where(function ($query) use ($pembayaran) {
            if ($pembayaran->tagihan_bulanan_id) {
                $query->where('tagihan_bulanan_id', $pembayaran->tagihan_bulanan_id);
            } else {
                $query->where('tagihan_terjadwal_id', $pembayaran->tagihan_terjadwal_id);
            }
        })
            ->where('nominal_pembayaran', $pembayaran->nominal_pembayaran)
            ->where('created_at', '>', now()->subMinutes(config('tagihan.payment_duplicate_window', 5)))
            ->first();

        if ($recentPayment) {
            Log::warning('Duplicate payment detected', [
                'existing_id' => $recentPayment->id_pembayaran,
                'nominal' => $pembayaran->nominal_pembayaran,
                'user' => auth()->user()->name ?? 'System'
            ]);
        }

        return true;
    }

    public function created(Pembayaran $pembayaran): void
    {
        DB::transaction(function () use ($pembayaran) {
            try {
                // Log audit
                AuditLog::logAction(
                    'pembayarans',
                    $pembayaran->id_pembayaran,
                    'created',
                    null,
                    $pembayaran->toArray()
                );

                // Update status tagihan
                if ($pembayaran->tagihan_terjadwal_id) {
                    $this->updateTagihanTerjadwalStatus($pembayaran);
                } elseif ($pembayaran->tagihan_bulanan_id) {
                    $this->paymentAllocationService->allocatePayment($pembayaran);
                }

                // Clear cache
                $this->clearCache($pembayaran);

                // NEW: Clear santri tunggakan cache
                $this->clearSantriTunggakanCache($pembayaran);

                Log::info('Pembayaran created successfully', [
                    'id' => $pembayaran->id_pembayaran,
                    'type' => $pembayaran->tagihan_bulanan_id ? 'bulanan' : 'terjadwal',
                    'nominal' => $pembayaran->nominal_pembayaran,
                    'created_by' => auth()->user()->name ?? 'System'
                ]);

            } catch (\Exception $e) {
                Log::error('Error in PembayaranObserver created', [
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'error' => $e->getMessage()
                ]);

                throw $e;
            }
        });
    }

    public function updated(Pembayaran $pembayaran): void
    {
        DB::transaction(function () use ($pembayaran) {
            try {
                // Log audit
                AuditLog::logAction(
                    'pembayarans',
                    $pembayaran->id_pembayaran,
                    'updated',
                    $pembayaran->getOriginal(),
                    $pembayaran->toArray()
                );

                // Recalculate tagihan status
                if ($pembayaran->tagihan_terjadwal_id) {
                    $this->updateTagihanTerjadwalStatus($pembayaran);
                } elseif ($pembayaran->tagihan_bulanan_id) {
                    if ($pembayaran->is_allocated) {
                        $santriId = $pembayaran->tagihanBulanan->santri_id;
                        $this->paymentAllocationService->recalculateTagihanStatus($santriId);
                    } else {
                        $pembayaran->tagihanBulanan->updateStatus();
                    }
                }

                // Clear cache
                $this->clearCache($pembayaran);

                // NEW: Clear santri tunggakan cache
                $this->clearSantriTunggakanCache($pembayaran);

            } catch (\Exception $e) {
                Log::error('Error in PembayaranObserver updated', [
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'error' => $e->getMessage()
                ]);

                throw $e;
            }
        });
    }

    public function deleting(Pembayaran $pembayaran): bool
    {
        // Store related IDs for status update after deletion
        $pembayaran->relatedTagihanId = $pembayaran->tagihan_terjadwal_id ?? $pembayaran->tagihan_bulanan_id;
        $pembayaran->relatedTagihanType = $pembayaran->tagihan_terjadwal_id ? 'terjadwal' : 'bulanan';

        return true;
    }

    public function deleted(Pembayaran $pembayaran): void
    {
        DB::transaction(function () use ($pembayaran) {
            try {
                // Log audit
                AuditLog::logAction(
                    'pembayarans',
                    $pembayaran->id_pembayaran,
                    'deleted',
                    $pembayaran->toArray(),
                    null
                );

                // Update tagihan status after payment deletion
                if ($pembayaran->relatedTagihanType === 'terjadwal') {
                    $tagihan = TagihanTerjadwal::find($pembayaran->relatedTagihanId);
                    if ($tagihan) {
                        $tagihan->updateStatus();
                    }
                } else {
                    if ($pembayaran->is_allocated) {
                        $allocations = $pembayaran->paymentAllocations;
                        foreach ($allocations as $allocation) {
                            if ($allocation->tagihanBulanan) {
                                $allocation->tagihanBulanan->updateStatus();
                            }
                        }
                    } else {
                        $tagihan = TagihanBulanan::find($pembayaran->relatedTagihanId);
                        if ($tagihan) {
                            $tagihan->updateStatus();
                        }
                    }
                }

                // Clear cache
                $this->clearCache($pembayaran);

                // NEW: Clear santri tunggakan cache
                $this->clearSantriTunggakanCache($pembayaran);

                Log::info('Pembayaran deleted', [
                    'id' => $pembayaran->id_pembayaran,
                    'deleted_by' => auth()->user()->name ?? 'System'
                ]);

            } catch (\Exception $e) {
                Log::error('Error in PembayaranObserver deleted', [
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }

    protected function updateTagihanTerjadwalStatus(Pembayaran $pembayaran): void
    {
        $tagihan = $pembayaran->tagihanTerjadwal;
        if ($tagihan) {
            $tagihan->updateStatus();
        }
    }

    protected function clearCache(Pembayaran $pembayaran): void
    {
        Cache::forget('dashboard_stats');

        if ($pembayaran->tagihan_bulanan_id) {
            $tagihan = $pembayaran->tagihanBulanan;
            if ($tagihan) {
                Cache::forget("santri_tagihan_{$tagihan->santri_id}");
                Cache::forget("monthly_stats_{$tagihan->tahun}_{$tagihan->bulan}");
            }
        }
    }

    // NEW: Clear santri tunggakan cache
    protected function clearSantriTunggakanCache(Pembayaran $pembayaran): void
    {
        try {
            // Get santri ID from various sources
            $santriId = null;

            // From direct tagihan relationship
            if ($pembayaran->tagihan_bulanan_id && $pembayaran->tagihanBulanan) {
                $santriId = $pembayaran->tagihanBulanan->santri_id;
            } elseif ($pembayaran->tagihan_terjadwal_id && $pembayaran->tagihanTerjadwal) {
                $santriId = $pembayaran->tagihanTerjadwal->santri_id;
            }

            // From payment allocations
            if (!$santriId && $pembayaran->paymentAllocations) {
                foreach ($pembayaran->paymentAllocations as $allocation) {
                    if ($allocation->tagihanBulanan) {
                        $santriId = $allocation->tagihanBulanan->santri_id;
                        break;
                    } elseif ($allocation->tagihanTerjadwal) {
                        $santriId = $allocation->tagihanTerjadwal->santri_id;
                        break;
                    }
                }
            }

            // Clear cache if santri ID found
            if ($santriId) {
                Cache::forget("santri_tunggakan_{$santriId}");

                Log::debug('Cleared santri tunggakan cache', [
                    'santri_id' => $santriId,
                    'pembayaran_id' => $pembayaran->id_pembayaran
                ]);
            }

        } catch (\Exception $e) {
            Log::warning('Failed to clear santri tunggakan cache', [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'error' => $e->getMessage()
            ]);
        }
    }
}

// =================================
// TagihanBulananObserver UPDATE
// =================================

namespace App\Observers;

use App\Models\TagihanBulanan;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TagihanBulananObserver
{
    public function creating(TagihanBulanan $tagihanBulanan): void
    {
        $this->clearCache($tagihanBulanan);
    }

    public function created(TagihanBulanan $tagihanBulanan): void
    {
        AuditLog::logAction(
            'tagihan_bulanans',
            $tagihanBulanan->id_tagihan_bulanan,
            'created',
            null,
            $tagihanBulanan->toArray()
        );

        Log::info('TagihanBulanan created', [
            'id' => $tagihanBulanan->id_tagihan_bulanan,
            'santri_id' => $tagihanBulanan->santri_id,
            'bulan' => $tagihanBulanan->bulan,
            'tahun' => $tagihanBulanan->tahun,
            'nominal' => $tagihanBulanan->nominal,
            'created_by' => auth()->user()->name ?? 'System',
            'ip' => request()->ip()
        ]);

        $this->clearCache($tagihanBulanan);

        // NEW: Clear santri tunggakan cache
        $this->clearSantriTunggakanCache($tagihanBulanan->santri_id);
    }

    public function updating(TagihanBulanan $tagihanBulanan): void
    {
        $tagihanBulanan->oldValues = $tagihanBulanan->getOriginal();
    }

    public function updated(TagihanBulanan $tagihanBulanan): void
    {
        $changes = $tagihanBulanan->getChanges();
        unset($changes['updated_at']);

        if (!empty($changes)) {
            AuditLog::logAction(
                'tagihan_bulanans',
                $tagihanBulanan->id_tagihan_bulanan,
                'updated',
                $tagihanBulanan->oldValues ?? [],
                $tagihanBulanan->toArray()
            );

            Log::info('TagihanBulanan updated', [
                'id' => $tagihanBulanan->id_tagihan_bulanan,
                'changes' => $changes,
                'updated_by' => auth()->user()->name ?? 'System',
                'ip' => request()->ip()
            ]);

            if (isset($changes['status'])) {
                Log::info('TagihanBulanan status changed', [
                    'id' => $tagihanBulanan->id_tagihan_bulanan,
                    'santri' => $tagihanBulanan->santri->nama_santri,
                    'from' => $tagihanBulanan->oldValues['status'] ?? 'unknown',
                    'to' => $tagihanBulanan->status,
                    'bulan' => $tagihanBulanan->bulan,
                    'tahun' => $tagihanBulanan->tahun
                ]);
            }
        }

        $this->clearCache($tagihanBulanan);

        // NEW: Clear santri tunggakan cache
        $this->clearSantriTunggakanCache($tagihanBulanan->santri_id);
    }

    public function deleting(TagihanBulanan $tagihanBulanan): bool
    {
        if (!$tagihanBulanan->canDelete()) {
            Log::warning('Attempt to delete TagihanBulanan with payments', [
                'id' => $tagihanBulanan->id_tagihan_bulanan,
                'attempted_by' => auth()->user()->name ?? 'System'
            ]);
            return false;
        }

        return true;
    }

    public function deleted(TagihanBulanan $tagihanBulanan): void
    {
        AuditLog::logAction(
            'tagihan_bulanans',
            $tagihanBulanan->id_tagihan_bulanan,
            'deleted',
            $tagihanBulanan->toArray(),
            null
        );

        Log::info('TagihanBulanan deleted', [
            'id' => $tagihanBulanan->id_tagihan_bulanan,
            'santri_id' => $tagihanBulanan->santri_id,
            'bulan' => $tagihanBulanan->bulan,
            'tahun' => $tagihanBulanan->tahun,
            'deleted_by' => auth()->user()->name ?? 'System',
            'ip' => request()->ip()
        ]);

        $this->clearCache($tagihanBulanan);

        // NEW: Clear santri tunggakan cache
        $this->clearSantriTunggakanCache($tagihanBulanan->santri_id);
    }

    public function restored(TagihanBulanan $tagihanBulanan): void
    {
        AuditLog::logAction(
            'tagihan_bulanans',
            $tagihanBulanan->id_tagihan_bulanan,
            'restored',
            null,
            $tagihanBulanan->toArray()
        );

        $this->clearCache($tagihanBulanan);

        // NEW: Clear santri tunggakan cache
        $this->clearSantriTunggakanCache($tagihanBulanan->santri_id);
    }

    public function forceDeleted(TagihanBulanan $tagihanBulanan): void
    {
        Log::warning('TagihanBulanan permanently deleted', [
            'id' => $tagihanBulanan->id_tagihan_bulanan,
            'santri_id' => $tagihanBulanan->santri_id,
            'deleted_by' => auth()->user()->name ?? 'System'
        ]);
    }

    protected function clearCache(TagihanBulanan $tagihanBulanan): void
    {
        Cache::forget('dashboard_stats');
        Cache::forget("dashboard_stats_{$tagihanBulanan->tahun}");
        Cache::forget("santri_tagihan_{$tagihanBulanan->santri_id}");
        Cache::forget("monthly_stats_{$tagihanBulanan->tahun}_{$tagihanBulanan->bulan}");
    }

    // NEW: Clear santri tunggakan cache
    protected function clearSantriTunggakanCache($santriId): void
    {
        if ($santriId) {
            Cache::forget("santri_tunggakan_{$santriId}");
        }
    }
}
