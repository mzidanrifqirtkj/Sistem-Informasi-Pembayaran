<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\TagihanBulanan;
use App\Models\TagihanTerjadwal;
use App\Models\PaymentAllocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentAllocationService
{
    /**
     * Alokasi pembayaran ke multiple tagihan (updated to support both types)
     */
    public function allocatePayment(Pembayaran $pembayaran)
    {
        try {
            DB::beginTransaction();

            // Get santri ID from either tagihan type
            $santriId = null;
            if ($pembayaran->tagihan_bulanan_id) {
                $santriId = $pembayaran->tagihanBulanan->santri_id;
            } elseif ($pembayaran->tagihan_terjadwal_id) {
                $santriId = $pembayaran->tagihanTerjadwal->santri_id;
            }

            if (!$santriId) {
                throw new \Exception('Santri ID not found');
            }

            $nominalPembayaran = $pembayaran->nominal_pembayaran;

            // For single tagihan payment
            if ($pembayaran->payment_type !== 'allocated') {
                if ($pembayaran->tagihan_bulanan_id) {
                    $this->updateSingleTagihanStatus($pembayaran->tagihanBulanan);
                } elseif ($pembayaran->tagihan_terjadwal_id) {
                    $this->updateTagihanTerjadwalStatus($pembayaran->tagihanTerjadwal);
                }
                DB::commit();
                return;
            }

            // For allocated payments, process allocations
            $this->processAllocatedPayment($pembayaran, $santriId);

            DB::commit();

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
     * Process allocated payment
     */
    protected function processAllocatedPayment(Pembayaran $pembayaran, $santriId)
    {
        // Get allocations for this payment
        $allocations = $pembayaran->paymentAllocations;

        foreach ($allocations as $allocation) {
            if ($allocation->tagihan_bulanan_id) {
                $tagihan = TagihanBulanan::find($allocation->tagihan_bulanan_id);
                if ($tagihan) {
                    $tagihan->updateStatus();
                }
            } elseif ($allocation->tagihan_terjadwal_id) {
                $tagihan = TagihanTerjadwal::find($allocation->tagihan_terjadwal_id);
                if ($tagihan) {
                    $tagihan->updateStatus();
                }
            }
        }

        // Log success
        Log::info('Payment allocation completed', [
            'pembayaran_id' => $pembayaran->id_pembayaran,
            'total_allocations' => $allocations->count()
        ]);
    }

    /**
     * Check if payment needs allocation
     */
    protected function needsAllocation(Pembayaran $pembayaran)
    {
        // This method is kept for backward compatibility
        // New flow handles allocation differently
        return $pembayaran->payment_type === 'allocated';
    }

    /**
     * Update single tagihan bulanan status
     */
    protected function updateSingleTagihanStatus(TagihanBulanan $tagihan)
    {
        $tagihan->updateStatus();
    }

    /**
     * Update single tagihan terjadwal status
     */
    protected function updateTagihanTerjadwalStatus(TagihanTerjadwal $tagihan)
    {
        $tagihan->updateStatus();
    }

    /**
     * Handle overpayment
     */
    protected function handleOverpayment(Pembayaran $pembayaran, $sisaPembayaran)
    {
        Log::info('Overpayment detected', [
            'pembayaran_id' => $pembayaran->id_pembayaran,
            'amount' => $sisaPembayaran
        ]);

        // Future: implement credit system or refund mechanism
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

        $tagihansBulanan = $query->get();
        foreach ($tagihansBulanan as $tagihan) {
            $tagihan->updateStatus();
        }

        // Also recalculate terjadwal
        $queryTerjadwal = TagihanTerjadwal::where('santri_id', $santriId);
        if ($tahun) {
            $queryTerjadwal->where('tahun', $tahun);
        }

        $tagihansTerjadwal = $queryTerjadwal->get();
        foreach ($tagihansTerjadwal as $tagihan) {
            $tagihan->updateStatus();
        }

        return $tagihansBulanan->count() + $tagihansTerjadwal->count();
    }

    /**
     * Void payment allocations
     */
    public function voidPaymentAllocations(Pembayaran $pembayaran)
    {
        if ($pembayaran->payment_type !== 'allocated') {
            return;
        }

        // Delete allocations
        $pembayaran->paymentAllocations()->delete();

        // Recalculate all affected tagihan
        $santriId = null;
        if ($pembayaran->tagihan_bulanan_id) {
            $santriId = $pembayaran->tagihanBulanan->santri_id;
        } elseif ($pembayaran->tagihan_terjadwal_id) {
            $santriId = $pembayaran->tagihanTerjadwal->santri_id;
        }

        if ($santriId) {
            $this->recalculateTagihanStatus($santriId);
        }
    }

    /**
     * Create payment allocation records
     */
    public function createAllocation(array $data)
    {
        return PaymentAllocation::create($data);
    }

    /**
     * Get allocation summary for reporting
     */
    public function getAllocationSummary($pembayaranId)
    {
        $pembayaran = Pembayaran::with(['paymentAllocations.tagihanBulanan', 'paymentAllocations.tagihanTerjadwal'])
            ->findOrFail($pembayaranId);

        $summary = [
            'total_allocated' => 0,
            'allocations' => []
        ];

        foreach ($pembayaran->paymentAllocations as $allocation) {
            $detail = [
                'amount' => $allocation->allocated_amount,
                'type' => $allocation->tagihan_type,
                'description' => $allocation->tagihan_detail
            ];

            if ($allocation->tagihan_bulanan_id) {
                $detail['tagihan'] = $allocation->tagihanBulanan;
                $detail['santri'] = $allocation->tagihanBulanan->santri;
            } elseif ($allocation->tagihan_terjadwal_id) {
                $detail['tagihan'] = $allocation->tagihanTerjadwal;
                $detail['santri'] = $allocation->tagihanTerjadwal->santri;
            }

            $summary['allocations'][] = $detail;
            $summary['total_allocated'] += $allocation->allocated_amount;
        }

        return $summary;
    }

    /**
     * Validate allocation data before processing
     */
    public function validateAllocation(array $allocations, $totalPayment)
    {
        $totalAllocated = 0;

        foreach ($allocations as $allocation) {
            if (!isset($allocation['allocated_amount']) || $allocation['allocated_amount'] <= 0) {
                throw new \Exception('Nominal alokasi harus lebih dari 0');
            }

            $totalAllocated += $allocation['allocated_amount'];
        }

        if ($totalAllocated > $totalPayment) {
            throw new \Exception('Total alokasi melebihi nominal pembayaran');
        }

        return true;
    }

    /**
     * Get pending allocations for santri
     */
    public function getPendingAllocations($santriId)
    {
        $pendingBulanan = TagihanBulanan::where('santri_id', $santriId)
            ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan_urutan', 'asc')
            ->get();

        $pendingTerjadwal = TagihanTerjadwal::where('santri_id', $santriId)
            ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
            ->orderBy('tahun', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        return [
            'bulanan' => $pendingBulanan,
            'terjadwal' => $pendingTerjadwal,
            'total_pending' => $pendingBulanan->sum('sisa_tagihan') + $pendingTerjadwal->sum('sisa_tagihan')
        ];
    }
}
