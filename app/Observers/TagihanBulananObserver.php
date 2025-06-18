<?php

namespace App\Observers;

use App\Models\TagihanBulanan;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TagihanBulananObserver
{
    /**
     * Handle the TagihanBulanan "creating" event.
     */
    public function creating(TagihanBulanan $tagihanBulanan): void
    {
        // Set default values sudah di model boot method

        // Clear cache
        $this->clearCache($tagihanBulanan);
    }

    /**
     * Handle the TagihanBulanan "created" event.
     */
    public function created(TagihanBulanan $tagihanBulanan): void
    {
        // Log audit
        AuditLog::logAction(
            'tagihan_bulanans',
            $tagihanBulanan->id_tagihan_bulanan,
            'created',
            null,
            $tagihanBulanan->toArray()
        );

        // Log detailed
        Log::info('TagihanBulanan created', [
            'id' => $tagihanBulanan->id_tagihan_bulanan,
            'santri_id' => $tagihanBulanan->santri_id,
            'bulan' => $tagihanBulanan->bulan,
            'tahun' => $tagihanBulanan->tahun,
            'nominal' => $tagihanBulanan->nominal,
            'created_by' => auth()->user()->name ?? 'System',
            'ip' => request()->ip()
        ]);

        // Clear cache
        $this->clearCache($tagihanBulanan);
    }

    /**
     * Handle the TagihanBulanan "updating" event.
     */
    public function updating(TagihanBulanan $tagihanBulanan): void
    {
        // Store original values for audit
        $tagihanBulanan->oldValues = $tagihanBulanan->getOriginal();
    }

    /**
     * Handle the TagihanBulanan "updated" event.
     */
    public function updated(TagihanBulanan $tagihanBulanan): void
    {
        // Get changed attributes
        $changes = $tagihanBulanan->getChanges();
        unset($changes['updated_at']); // Remove timestamp from changes

        if (!empty($changes)) {
            // Log audit
            AuditLog::logAction(
                'tagihan_bulanans',
                $tagihanBulanan->id_tagihan_bulanan,
                'updated',
                $tagihanBulanan->oldValues ?? [],
                $tagihanBulanan->toArray()
            );

            // Log detailed changes
            Log::info('TagihanBulanan updated', [
                'id' => $tagihanBulanan->id_tagihan_bulanan,
                'changes' => $changes,
                'updated_by' => auth()->user()->name ?? 'System',
                'ip' => request()->ip()
            ]);

            // If status changed, log special
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

        // Clear cache
        $this->clearCache($tagihanBulanan);
    }

    /**
     * Handle the TagihanBulanan "deleting" event.
     */
    public function deleting(TagihanBulanan $tagihanBulanan): bool
    {
        // Check if can delete
        if (!$tagihanBulanan->canDelete()) {
            Log::warning('Attempt to delete TagihanBulanan with payments', [
                'id' => $tagihanBulanan->id_tagihan_bulanan,
                'attempted_by' => auth()->user()->name ?? 'System'
            ]);
            return false; // Cancel delete
        }

        return true;
    }

    /**
     * Handle the TagihanBulanan "deleted" event.
     */
    public function deleted(TagihanBulanan $tagihanBulanan): void
    {
        // Log audit
        AuditLog::logAction(
            'tagihan_bulanans',
            $tagihanBulanan->id_tagihan_bulanan,
            'deleted',
            $tagihanBulanan->toArray(),
            null
        );

        // Log detailed
        Log::info('TagihanBulanan deleted', [
            'id' => $tagihanBulanan->id_tagihan_bulanan,
            'santri_id' => $tagihanBulanan->santri_id,
            'bulan' => $tagihanBulanan->bulan,
            'tahun' => $tagihanBulanan->tahun,
            'deleted_by' => auth()->user()->name ?? 'System',
            'ip' => request()->ip()
        ]);

        // Clear cache
        $this->clearCache($tagihanBulanan);
    }

    /**
     * Handle the TagihanBulanan "restored" event.
     */
    public function restored(TagihanBulanan $tagihanBulanan): void
    {
        // Log audit
        AuditLog::logAction(
            'tagihan_bulanans',
            $tagihanBulanan->id_tagihan_bulanan,
            'restored',
            null,
            $tagihanBulanan->toArray()
        );

        // Clear cache
        $this->clearCache($tagihanBulanan);
    }

    /**
     * Handle the TagihanBulanan "forceDeleted" event.
     */
    public function forceDeleted(TagihanBulanan $tagihanBulanan): void
    {
        // Log permanent deletion
        Log::warning('TagihanBulanan permanently deleted', [
            'id' => $tagihanBulanan->id_tagihan_bulanan,
            'santri_id' => $tagihanBulanan->santri_id,
            'deleted_by' => auth()->user()->name ?? 'System'
        ]);
    }

    /**
     * Clear related caches
     */
    protected function clearCache(TagihanBulanan $tagihanBulanan): void
    {
        // Clear dashboard cache
        Cache::forget('dashboard_stats');
        Cache::forget("dashboard_stats_{$tagihanBulanan->tahun}");

        // Clear santri specific cache
        Cache::forget("santri_tagihan_{$tagihanBulanan->santri_id}");
        Cache::forget("santri_tunggakan_{$tagihanBulanan->santri_id}");

        // Clear monthly stats cache
        Cache::forget("monthly_stats_{$tagihanBulanan->tahun}_{$tagihanBulanan->bulan}");
    }
}
