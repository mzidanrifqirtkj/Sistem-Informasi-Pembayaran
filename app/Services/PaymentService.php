<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\TagihanBulanan;
use App\Models\TagihanTerjadwal;
use App\Models\Santri;
use App\Models\GenerateLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PaymentService
{
    protected $paymentAllocationService;
    protected $validationService;

    public function __construct(
        PaymentAllocationService $paymentAllocationService,
        PaymentValidationService $validationService
    ) {
        $this->paymentAllocationService = $paymentAllocationService;
        $this->validationService = $validationService;
    }

    /**
     * Generate nomor kwitansi
     */
    public function generateReceiptNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');

        // Get last number for current month
        $lastReceipt = Pembayaran::where('receipt_number', 'like', "KWT/{$year}/{$month}/%")
            ->orderBy('receipt_number', 'desc')
            ->first();

        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt->receipt_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("KWT/%s/%s/%04d", $year, $month, $newNumber);
    }

    /**
     * Get tagihan santri untuk pembayaran
     */
    public function getTagihanSantri(Santri $santri): array
    {
        // Validasi santri aktif
        if ($santri->status !== 'aktif') {
            throw new \Exception('Santri sudah tidak aktif');
        }

        // Get tagihan bulanan yang belum lunas
        $tagihanBulanan = TagihanBulanan::where('santri_id', $santri->id_santri)
            ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan_urutan', 'asc')
            ->get();

        // Get tagihan terjadwal yang belum lunas
        $tagihanTerjadwal = TagihanTerjadwal::where('santri_id', $santri->id_santri)
            ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
            ->orderBy('tahun', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Calculate total tunggakan
        $totalTunggakanBulanan = $tagihanBulanan->sum('sisa_tagihan');
        $totalTunggakanTerjadwal = $tagihanTerjadwal->sum('sisa_tagihan');
        $totalTunggakan = $totalTunggakanBulanan + $totalTunggakanTerjadwal;

        return [
            'santri' => $santri,
            'tagihan_bulanan' => $tagihanBulanan,
            'tagihan_terjadwal' => $tagihanTerjadwal,
            'total_tunggakan_bulanan' => $totalTunggakanBulanan,
            'total_tunggakan_terjadwal' => $totalTunggakanTerjadwal,
            'total_tunggakan' => $totalTunggakan
        ];
    }

    /**
     * Preview alokasi pembayaran
     */
    public function previewPaymentAllocation($santriId, $nominalPembayaran, $selectedTagihan = []): array
    {
        $santri = Santri::findOrFail($santriId);

        // Validasi
        $this->validationService->validatePaymentAmount($nominalPembayaran);
        $this->validationService->validateDuplicatePayment($santriId, $nominalPembayaran);

        $allocations = [];
        $sisaPembayaran = $nominalPembayaran;

        // Debug log
        \Log::info('Preview Payment Allocation', [
            'santri_id' => $santriId,
            'nominal' => $nominalPembayaran,
            'selected_tagihan' => $selectedTagihan
        ]);

        // Jika ada tagihan yang dipilih
        if (!empty($selectedTagihan)) {
            foreach ($selectedTagihan as $item) {
                if ($sisaPembayaran <= 0)
                    break;

                // Pastikan struktur data benar
                if (!isset($item['type']) || !isset($item['id'])) {
                    \Log::warning('Invalid tagihan item structure', ['item' => $item]);
                    continue;
                }

                $tagihan = $this->getTagihanById($item['type'], $item['id']);
                if (!$tagihan) {
                    \Log::warning('Tagihan not found', ['type' => $item['type'], 'id' => $item['id']]);
                    continue;
                }

                // Load relationships untuk tagihan terjadwal
                if ($item['type'] === 'terjadwal') {
                    $tagihan->load('biayaSantri.daftarBiaya.kategoriBiaya');
                }

                $sisaTagihan = $tagihan->sisa_tagihan;
                $allocationAmount = min($sisaPembayaran, $sisaTagihan);

                $allocations[] = [
                    'type' => $item['type'],
                    'tagihan' => $tagihan,
                    'allocated_amount' => $allocationAmount,
                    'sisa_tagihan' => $sisaTagihan - $allocationAmount,
                    'status_after' => $this->calculateStatusAfter($sisaTagihan, $allocationAmount)
                ];

                $sisaPembayaran -= $allocationAmount;
            }
        } else {
            // Alokasi otomatis berdasarkan prioritas
            $allocations = $this->autoAllocatePayment($santri, $nominalPembayaran);
            $sisaPembayaran = $nominalPembayaran - collect($allocations)->sum('allocated_amount');
        }

        return [
            'santri' => $santri,
            'nominal_pembayaran' => $nominalPembayaran,
            'allocations' => $allocations,
            'sisa_pembayaran' => $sisaPembayaran,
            'total_allocated' => collect($allocations)->sum('allocated_amount')
        ];
    }

    /**
     * Auto allocate payment berdasarkan prioritas
     */
    protected function autoAllocatePayment(Santri $santri, $nominalPembayaran): array
    {
        $allocations = [];
        $sisaPembayaran = $nominalPembayaran;

        // Priority 1: Tagihan bulanan dari yang terlama
        $tagihanBulanan = TagihanBulanan::where('santri_id', $santri->id_santri)
            ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan_urutan', 'asc')
            ->get();

        foreach ($tagihanBulanan as $tagihan) {
            if ($sisaPembayaran <= 0)
                break;

            // Validasi urutan pembayaran bulanan
            if (!$this->validationService->validateMonthlyPaymentSequence($santri, $tagihan)) {
                continue;
            }

            $sisaTagihan = $tagihan->sisa_tagihan;
            $allocationAmount = min($sisaPembayaran, $sisaTagihan);

            $allocations[] = [
                'type' => 'bulanan',
                'tagihan' => $tagihan,
                'allocated_amount' => $allocationAmount,
                'sisa_tagihan' => $sisaTagihan - $allocationAmount,
                'status_after' => $this->calculateStatusAfter($sisaTagihan, $allocationAmount)
            ];

            $sisaPembayaran -= $allocationAmount;
        }

        // Priority 2: Tagihan terjadwal
        if ($sisaPembayaran > 0) {
            $tagihanTerjadwal = TagihanTerjadwal::where('santri_id', $santri->id_santri)
                ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
                ->orderBy('tahun', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($tagihanTerjadwal as $tagihan) {
                if ($sisaPembayaran <= 0)
                    break;

                $sisaTagihan = $tagihan->sisa_tagihan;
                $allocationAmount = min($sisaPembayaran, $sisaTagihan);

                $allocations[] = [
                    'type' => 'terjadwal',
                    'tagihan' => $tagihan,
                    'allocated_amount' => $allocationAmount,
                    'sisa_tagihan' => $sisaTagihan - $allocationAmount,
                    'status_after' => $this->calculateStatusAfter($sisaTagihan, $allocationAmount)
                ];

                $sisaPembayaran -= $allocationAmount;
            }
        }

        return $allocations;
    }

    /**
     * Process pembayaran
     */
    public function processPayment(array $data): Pembayaran
    {
        try {
            DB::beginTransaction();

            // Generate receipt number
            $data['receipt_number'] = $this->generateReceiptNumber();
            $data['created_by_id'] = auth()->id();

            // Create pembayaran record
            $pembayaran = Pembayaran::create([
                'nominal_pembayaran' => $data['nominal_pembayaran'],
                'tanggal_pembayaran' => $data['tanggal_pembayaran'] ?? now(),
                'payment_note' => $data['payment_note'] ?? null,
                'receipt_number' => $data['receipt_number'],
                'created_by_id' => $data['created_by_id']
            ]);

            // Process allocations
            if (!empty($data['allocations'])) {
                $this->processAllocations($pembayaran, $data['allocations']);
            }

            // Handle overpayment
            if (isset($data['sisa_pembayaran']) && $data['sisa_pembayaran'] > 0) {
                $this->handleOverpayment($pembayaran, $data['sisa_pembayaran']);
            }

            DB::commit();

            Log::info('Pembayaran berhasil diproses', [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'nominal' => $pembayaran->nominal_pembayaran,
                'receipt' => $pembayaran->receipt_number
            ]);

            return $pembayaran;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing payment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process payment allocations
     */
    protected function processAllocations(Pembayaran $pembayaran, array $allocations): void
    {
        $order = 1;

        foreach ($allocations as $allocation) {
            // Set pembayaran ID berdasarkan type
            if ($allocation['type'] === 'bulanan') {
                $pembayaran->tagihan_bulanan_id = $allocation['tagihan']->id_tagihan_bulanan;
            } else {
                $pembayaran->tagihan_terjadwal_id = $allocation['tagihan']->id_tagihan_terjadwal;
            }

            // Jika ini pembayaran pertama, update pembayaran record
            if ($order === 1) {
                $pembayaran->save();
            }

            // Create allocation record jika multiple tagihan
            if (count($allocations) > 1) {
                \App\Models\PaymentAllocation::create([
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'tagihan_bulanan_id' => $allocation['type'] === 'bulanan' ?
                        $allocation['tagihan']->id_tagihan_bulanan : null,
                    'tagihan_terjadwal_id' => $allocation['type'] === 'terjadwal' ?
                        $allocation['tagihan']->id_tagihan_terjadwal : null,
                    'allocated_amount' => $allocation['allocated_amount'],
                    'allocation_order' => $order++
                ]);
            }
        }

        // Set payment type
        if (count($allocations) > 1) {
            $pembayaran->payment_type = 'allocated';
            $pembayaran->total_allocations = count($allocations);
            $pembayaran->save();
        }
    }

    /**
     * Void pembayaran
     */
    public function voidPayment(Pembayaran $pembayaran, string $reason): bool
    {
        try {
            DB::beginTransaction();

            // Validasi
            if ($pembayaran->is_void) {
                throw new \Exception('Pembayaran sudah di-void sebelumnya');
            }

            // Check if can void (within 24 hours)
            if ($pembayaran->created_at->diffInHours(now()) > 24) {
                throw new \Exception('Pembayaran hanya bisa di-void dalam 24 jam');
            }

            // Update pembayaran
            $pembayaran->update([
                'is_void' => true,
                'voided_at' => now(),
                'voided_by' => auth()->id(),
                'void_reason' => $reason
            ]);

            // Delete allocations
            if ($pembayaran->payment_type === 'allocated') {
                $pembayaran->paymentAllocations()->delete();
            }

            // Status tagihan akan di-update oleh observer

            DB::commit();

            Log::info('Pembayaran berhasil di-void', [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'voided_by' => auth()->user()->name,
                'reason' => $reason
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error void payment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Helper methods
     */
    protected function getTagihanById($type, $id)
    {
        try {
            if ($type === 'bulanan') {
                return TagihanBulanan::with('santri')->findOrFail($id);
            } else {
                return TagihanTerjadwal::with(['santri', 'biayaSantri.daftarBiaya.kategoriBiaya'])->findOrFail($id);
            }
        } catch (\Exception $e) {
            \Log::error('Error getting tagihan', [
                'type' => $type,
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    protected function calculateStatusAfter($sisaTagihan, $allocationAmount): string
    {
        $remaining = $sisaTagihan - $allocationAmount;

        if ($remaining <= 0) {
            return 'lunas';
        } elseif ($allocationAmount > 0) {
            return 'dibayar_sebagian';
        } else {
            return 'belum_lunas';
        }
    }

    protected function handleOverpayment(Pembayaran $pembayaran, $sisaPembayaran): void
    {
        Log::info('Kelebihan pembayaran terdeteksi', [
            'pembayaran_id' => $pembayaran->id_pembayaran,
            'kelebihan' => $sisaPembayaran
        ]);

        // Future: implement credit/refund system
    }
}


