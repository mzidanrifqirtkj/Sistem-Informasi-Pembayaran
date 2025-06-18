<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\TagihanBulanan;
use App\Models\PaymentAllocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentAllocationService
{
    /**
     * Alokasi pembayaran ke multiple tagihan
     */
    public function allocatePayment(Pembayaran $pembayaran)
    {
        // Skip jika bukan tagihan bulanan
        if (!$pembayaran->tagihan_bulanan_id) {
            return;
        }

        try {
            DB::beginTransaction();

            $santriId = $pembayaran->tagihanBulanan->santri_id;
            $nominalPembayaran = $pembayaran->nominal_pembayaran;

            // Cek apakah perlu alokasi
            if (!$this->needsAllocation($pembayaran)) {
                // Single tagihan payment
                $this->updateSingleTagihanStatus($pembayaran->tagihanBulanan);
                DB::commit();
                return;
            }

            // Get tagihan yang belum lunas, urut dari yang terlama
            $tagihanBelumLunas = TagihanBulanan::where('santri_id', $santriId)
                ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
                ->orderBy('tahun', 'asc')
                ->orderBy('bulan_urutan', 'asc')
                ->lockForUpdate() // Prevent concurrent access
                ->get();

            if ($tagihanBelumLunas->isEmpty()) {
                DB::commit();
                return;
            }

            // Set payment type
            $pembayaran->payment_type = 'allocated';
            $pembayaran->save();

            $sisaPembayaran = $nominalPembayaran;
            $allocationOrder = 1;
            $allocations = [];

            foreach ($tagihanBelumLunas as $tagihan) {
                if ($sisaPembayaran <= 0)
                    break;

                $sisaTagihan = $tagihan->nominal - $tagihan->total_pembayaran;

                if ($sisaTagihan <= 0)
                    continue;

                $allocationAmount = min($sisaPembayaran, $sisaTagihan);

                // Create allocation record
                $allocation = PaymentAllocation::create([
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'tagihan_bulanan_id' => $tagihan->id_tagihan_bulanan,
                    'allocated_amount' => $allocationAmount,
                    'allocation_order' => $allocationOrder++
                ]);

                $allocations[] = $allocation;
                $sisaPembayaran -= $allocationAmount;

                // Update tagihan status
                $tagihan->updateStatus();

                // Log allocation
                Log::info('Payment allocated', [
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'tagihan_id' => $tagihan->id_tagihan_bulanan,
                    'amount' => $allocationAmount,
                    'remaining' => $sisaPembayaran
                ]);
            }

            // Update total allocations
            $pembayaran->total_allocations = count($allocations);
            $pembayaran->save();

            // Handle overpayment jika ada sisa
            if ($sisaPembayaran > 0) {
                $this->handleOverpayment($pembayaran, $sisaPembayaran);
            }

            DB::commit();

            // Log success
            $this->logAllocationSuccess($pembayaran, $allocations);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment allocation failed', [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Check if payment needs allocation
     */
    protected function needsAllocation(Pembayaran $pembayaran)
    {
        $santriId = $pembayaran->tagihanBulanan->santri_id;

        // Get total tunggakan
        $totalTunggakan = TagihanBulanan::where('santri_id', $santriId)
            ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
            ->sum(DB::raw('nominal - COALESCE((
                SELECT SUM(nominal_pembayaran)
                FROM pembayarans
                WHERE tagihan_bulanan_id = tagihan_bulanans.id_tagihan_bulanan
            ), 0) - COALESCE((
                SELECT SUM(allocated_amount)
                FROM payment_allocations
                WHERE tagihan_bulanan_id = tagihan_bulanans.id_tagihan_bulanan
            ), 0)'));

        // If payment lebih dari tagihan current, needs allocation
        return $pembayaran->nominal_pembayaran > $pembayaran->tagihanBulanan->sisa_tagihan;
    }

    /**
     * Update single tagihan status
     */
    protected function updateSingleTagihanStatus(TagihanBulanan $tagihan)
    {
        $tagihan->updateStatus();
    }

    /**
     * Handle overpayment
     */
    protected function handleOverpayment(Pembayaran $pembayaran, $sisaPembayaran)
    {
        // Option untuk future: create credit note atau auto-generate tagihan bulan depan
        Log::info('Overpayment detected', [
            'pembayaran_id' => $pembayaran->id_pembayaran,
            'amount' => $sisaPembayaran
        ]);

        // Untuk sekarang, log saja
        // Future: implement credit system
    }

    /**
     * Log successful allocation
     */
    protected function logAllocationSuccess(Pembayaran $pembayaran, array $allocations)
    {
        $summary = collect($allocations)->map(function ($allocation) {
            return [
                'tagihan_id' => $allocation->tagihan_bulanan_id,
                'amount' => $allocation->allocated_amount
            ];
        })->toArray();

        Log::info('Payment allocation completed', [
            'pembayaran_id' => $pembayaran->id_pembayaran,
            'total_allocations' => count($allocations),
            'summary' => $summary
        ]);
    }

    /**
     * Recalculate all tagihan status for santri
     */
    public function recalculateTagihanStatus($santriId, $tahun = null)
    {
        $query = TagihanBulanan::where('santri_id', $santriId);

        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        $tagihans = $query->get();

        foreach ($tagihans as $tagihan) {
            $tagihan->updateStatus();
        }

        return $tagihans->count();
    }
}
