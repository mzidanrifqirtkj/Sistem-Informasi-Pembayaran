<?php

namespace App\Observers;

use App\Models\TagihanTerjadwal;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagihanTerjadwalObserver
{
    /**
     * Handle the TagihanTerjadwal "created" event.
     */
    public function created(TagihanTerjadwal $tagihanTerjadwal): void
    {
        // Log creation for audit
        Log::info("TagihanTerjadwal created", [
            'id' => $tagihanTerjadwal->id_tagihan_terjadwal,
            'santri_id' => $tagihanTerjadwal->santri_id,
            'nominal' => $tagihanTerjadwal->nominal
        ]);
    }

    /**
     * Handle the TagihanTerjadwal "updated" event.
     */
    public function updated(TagihanTerjadwal $tagihanTerjadwal): void
    {
        // Log update for audit
        Log::info("TagihanTerjadwal updated", [
            'id' => $tagihanTerjadwal->id_tagihan_terjadwal,
            'changes' => $tagihanTerjadwal->getChanges()
        ]);
    }

    /**
     * Handle the TagihanTerjadwal "deleted" event.
     */
    public function deleted(TagihanTerjadwal $tagihanTerjadwal): void
    {
        try {
            // When TagihanTerjadwal is soft deleted, we should consider
            // what to do with related Pembayarans
            // For now, we'll just log the deletion
            Log::info("TagihanTerjadwal soft deleted", [
                'id' => $tagihanTerjadwal->id_tagihan_terjadwal,
                'santri_id' => $tagihanTerjadwal->santri_id,
                'related_pembayarans_count' => $tagihanTerjadwal->pembayarans()->count()
            ]);

        } catch (\Exception $e) {
            Log::error("Error in TagihanTerjadwal deleted observer: " . $e->getMessage());
        }
    }

    /**
     * Handle the TagihanTerjadwal "restored" event.
     */
    public function restored(TagihanTerjadwal $tagihanTerjadwal): void
    {
        Log::info("TagihanTerjadwal restored", [
            'id' => $tagihanTerjadwal->id_tagihan_terjadwal,
            'santri_id' => $tagihanTerjadwal->santri_id
        ]);

        // Re-calculate status when restored
        $this->updateTagihanStatus($tagihanTerjadwal);
    }

    /**
     * Handle the TagihanTerjadwal "force deleted" event.
     */
    public function forceDeleted(TagihanTerjadwal $tagihanTerjadwal): void
    {
        Log::warning("TagihanTerjadwal force deleted", [
            'id' => $tagihanTerjadwal->id_tagihan_terjadwal,
            'santri_id' => $tagihanTerjadwal->santri_id
        ]);
    }

    /**
     * Update tagihan status based on related pembayarans
     */
    public function updateTagihanStatus(TagihanTerjadwal $tagihanTerjadwal): void
    {
        try {
            DB::beginTransaction();

            // Calculate total pembayaran for this tagihan
            $totalPembayaran = Pembayaran::where('tagihan_terjadwal_id', $tagihanTerjadwal->id_tagihan_terjadwal)
                ->sum('nominal_pembayaran');

            $nominalTagihan = $tagihanTerjadwal->nominal;

            // Determine new status
            $newStatus = $this->calculateStatus($totalPembayaran, $nominalTagihan);

            // Update only if status changed
            if ($tagihanTerjadwal->status !== $newStatus) {
                $tagihanTerjadwal->update(['status' => $newStatus]);

                Log::info("TagihanTerjadwal status updated", [
                    'id' => $tagihanTerjadwal->id_tagihan_terjadwal,
                    'old_status' => $tagihanTerjadwal->getOriginal('status'),
                    'new_status' => $newStatus,
                    'total_pembayaran' => $totalPembayaran,
                    'nominal_tagihan' => $nominalTagihan
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating TagihanTerjadwal status: " . $e->getMessage(), [
                'tagihan_id' => $tagihanTerjadwal->id_tagihan_terjadwal,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to trigger rollback in calling transaction
            throw $e;
        }
    }

    /**
     * Calculate status based on payment amount
     */
    private function calculateStatus(float $totalPembayaran, float $nominalTagihan): string
    {
        if ($totalPembayaran == 0) {
            return 'belum_lunas';
        } elseif ($totalPembayaran >= $nominalTagihan) {
            return 'lunas';
        } else {
            return 'dibayar_sebagian';
        }
    }
}
