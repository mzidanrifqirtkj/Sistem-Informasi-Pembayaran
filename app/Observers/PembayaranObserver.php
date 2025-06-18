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

    /**
     * Handle the Pembayaran "creating" event.
     */
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

            // In production, you might want to throw exception
            // throw new \Exception('Pembayaran duplikat terdeteksi');
        }

        return true;
    }

    /**
     * Handle the Pembayaran "created" event.
     */
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
                    // Check if needs allocation
                    $this->paymentAllocationService->allocatePayment($pembayaran);
                }

                // Clear cache
                $this->clearCache($pembayaran);

                // Log success
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

    /**
     * Handle the Pembayaran "updated" event.
     */
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
                    // For allocated payments, need to recalculate all affected tagihan
                    if ($pembayaran->is_allocated) {
                        $santriId = $pembayaran->tagihanBulanan->santri_id;
                        $this->paymentAllocationService->recalculateTagihanStatus($santriId);
                    } else {
                        $pembayaran->tagihanBulanan->updateStatus();
                    }
                }

                // Clear cache
                $this->clearCache($pembayaran);

            } catch (\Exception $e) {
                Log::error('Error in PembayaranObserver updated', [
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'error' => $e->getMessage()
                ]);

                throw $e;
            }
        });
    }

    /**
     * Handle the Pembayaran "deleting" event.
     */
    public function deleting(Pembayaran $pembayaran): bool
    {
        // Store related IDs for status update after deletion
        $pembayaran->relatedTagihanId = $pembayaran->tagihan_terjadwal_id ?? $pembayaran->tagihan_bulanan_id;
        $pembayaran->relatedTagihanType = $pembayaran->tagihan_terjadwal_id ? 'terjadwal' : 'bulanan';

        return true;
    }

    /**
     * Handle the Pembayaran "deleted" event.
     */
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
                    // For allocated payments, recalculate all
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

    /**
     * Update TagihanTerjadwal status
     */
    protected function updateTagihanTerjadwalStatus(Pembayaran $pembayaran): void
    {
        $tagihan = $pembayaran->tagihanTerjadwal;
        if ($tagihan) {
            $tagihan->updateStatus();
        }
    }

    /**
     * Clear related caches
     */
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
}
