<?php

namespace App\Observers;

use App\Models\Pembayaran;
use App\Models\TagihanTerjadwal;
use App\Models\TagihanBulanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranObserver
{
    /**
     * Handle the Pembayaran "created" event.
     */
    public function created(Pembayaran $pembayaran): void
    {
        try {
            // Update related TagihanTerjadwal status
            if ($pembayaran->tagihan_terjadwal_id) {
                $this->updateTagihanTerjadwalStatus($pembayaran->tagihan_terjadwal_id);
            }

            // Update related TagihanBulanan status
            if ($pembayaran->tagihan_bulanan_id) {
                $this->updateTagihanBulananStatus($pembayaran->tagihan_bulanan_id);
            }

            Log::info("Pembayaran created and status updated", [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'tagihan_terjadwal_id' => $pembayaran->tagihan_terjadwal_id,
                'tagihan_bulanan_id' => $pembayaran->tagihan_bulanan_id,
                'nominal' => $pembayaran->nominal_pembayaran
            ]);

        } catch (\Exception $e) {
            Log::error("Error in Pembayaran created observer: " . $e->getMessage());
            // Re-throw to trigger rollback
            throw $e;
        }
    }

    /**
     * Handle the Pembayaran "updated" event.
     */
    public function updated(Pembayaran $pembayaran): void
    {
        try {
            // Check if nominal_pembayaran was changed
            if ($pembayaran->wasChanged('nominal_pembayaran')) {
                // Update related TagihanTerjadwal status
                if ($pembayaran->tagihan_terjadwal_id) {
                    $this->updateTagihanTerjadwalStatus($pembayaran->tagihan_terjadwal_id);
                }

                // Update related TagihanBulanan status
                if ($pembayaran->tagihan_bulanan_id) {
                    $this->updateTagihanBulananStatus($pembayaran->tagihan_bulanan_id);
                }

                Log::info("Pembayaran updated and status recalculated", [
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'old_nominal' => $pembayaran->getOriginal('nominal_pembayaran'),
                    'new_nominal' => $pembayaran->nominal_pembayaran
                ]);
            }

            // Check if tagihan relationship was changed
            if ($pembayaran->wasChanged(['tagihan_terjadwal_id', 'tagihan_bulanan_id'])) {
                // Update old tagihan status
                $oldTerjadwalId = $pembayaran->getOriginal('tagihan_terjadwal_id');
                $oldBulananId = $pembayaran->getOriginal('tagihan_bulanan_id');

                if ($oldTerjadwalId) {
                    $this->updateTagihanTerjadwalStatus($oldTerjadwalId);
                }
                if ($oldBulananId) {
                    $this->updateTagihanBulananStatus($oldBulananId);
                }

                // Update new tagihan status
                if ($pembayaran->tagihan_terjadwal_id) {
                    $this->updateTagihanTerjadwalStatus($pembayaran->tagihan_terjadwal_id);
                }
                if ($pembayaran->tagihan_bulanan_id) {
                    $this->updateTagihanBulananStatus($pembayaran->tagihan_bulanan_id);
                }
            }

        } catch (\Exception $e) {
            Log::error("Error in Pembayaran updated observer: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle the Pembayaran "deleted" event.
     */
    public function deleted(Pembayaran $pembayaran): void
    {
        try {
            // Update related TagihanTerjadwal status
            if ($pembayaran->tagihan_terjadwal_id) {
                $this->updateTagihanTerjadwalStatus($pembayaran->tagihan_terjadwal_id);
            }

            // Update related TagihanBulanan status
            if ($pembayaran->tagihan_bulanan_id) {
                $this->updateTagihanBulananStatus($pembayaran->tagihan_bulanan_id);
            }

            Log::info("Pembayaran deleted and status updated", [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'tagihan_terjadwal_id' => $pembayaran->tagihan_terjadwal_id,
                'tagihan_bulanan_id' => $pembayaran->tagihan_bulanan_id,
                'nominal' => $pembayaran->nominal_pembayaran
            ]);

        } catch (\Exception $e) {
            Log::error("Error in Pembayaran deleted observer: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update TagihanTerjadwal status based on payments
     */
    private function updateTagihanTerjadwalStatus(int $tagihanTerjadwalId): void
    {
        try {
            $tagihan = TagihanTerjadwal::find($tagihanTerjadwalId);
            if (!$tagihan) {
                Log::warning("TagihanTerjadwal not found for status update", ['id' => $tagihanTerjadwalId]);
                return;
            }

            // Calculate total pembayaran
            $totalPembayaran = Pembayaran::where('tagihan_terjadwal_id', $tagihanTerjadwalId)
                ->sum('nominal_pembayaran');

            $nominalTagihan = $tagihan->nominal;

            // Calculate new status
            $newStatus = $this->calculateStatus($totalPembayaran, $nominalTagihan);

            // Update only if status changed
            if ($tagihan->status !== $newStatus) {
                $tagihan->update(['status' => $newStatus]);

                Log::info("TagihanTerjadwal status auto-updated", [
                    'id' => $tagihanTerjadwalId,
                    'old_status' => $tagihan->getOriginal('status'),
                    'new_status' => $newStatus,
                    'total_pembayaran' => $totalPembayaran,
                    'nominal_tagihan' => $nominalTagihan
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Error updating TagihanTerjadwal status in observer", [
                'tagihan_id' => $tagihanTerjadwalId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update TagihanBulanan status based on payments
     */
    private function updateTagihanBulananStatus(int $tagihanBulananId): void
    {
        try {
            $tagihan = TagihanBulanan::find($tagihanBulananId);
            if (!$tagihan) {
                Log::warning("TagihanBulanan not found for status update", ['id' => $tagihanBulananId]);
                return;
            }

            // Calculate total pembayaran
            $totalPembayaran = Pembayaran::where('tagihan_bulanan_id', $tagihanBulananId)
                ->sum('nominal_pembayaran');

            // Assuming TagihanBulanan has similar structure
            $nominalTagihan = $tagihan->nominal ?? $tagihan->calculateNominal();

            // Calculate new status
            $newStatus = $this->calculateStatus($totalPembayaran, $nominalTagihan);

            // Update only if status changed
            if ($tagihan->status !== $newStatus) {
                $tagihan->update(['status' => $newStatus]);

                Log::info("TagihanBulanan status auto-updated", [
                    'id' => $tagihanBulananId,
                    'old_status' => $tagihan->getOriginal('status'),
                    'new_status' => $newStatus,
                    'total_pembayaran' => $totalPembayaran,
                    'nominal_tagihan' => $nominalTagihan
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Error updating TagihanBulanan status in observer", [
                'tagihan_id' => $tagihanBulananId,
                'error' => $e->getMessage()
            ]);
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
