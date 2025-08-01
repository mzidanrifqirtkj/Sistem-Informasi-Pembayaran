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

            // Validate total allocation
            if (!empty($data['allocations'])) {
                $totalAllocated = collect($data['allocations'])->sum('allocated_amount');
                $sisaPembayaran = $data['sisa_pembayaran'] ?? 0;

                if (($totalAllocated + $sisaPembayaran) != $data['nominal_pembayaran']) {
                    throw new \Exception(
                        "Total alokasi ({$totalAllocated}) + sisa ({$sisaPembayaran}) tidak sama dengan nominal pembayaran ({$data['nominal_pembayaran']})"
                    );
                }
            }

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

            // Process allocations (pure allocation system)
            if (!empty($data['allocations'])) {
                $this->processAllocations($pembayaran, $data['allocations']);
            }

            DB::commit();

            Log::info('Payment processed successfully with pure allocation', [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'nominal' => $pembayaran->nominal_pembayaran,
                'allocations_count' => count($data['allocations'] ?? []),
                'sisa_pembayaran' => $data['sisa_pembayaran'] ?? 0,
                'receipt' => $pembayaran->receipt_number
            ]);

            return $pembayaran;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing payment: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Process payment allocations - Using existing model methods
     */
    protected function processAllocations(Pembayaran $pembayaran, array $allocations): void
    {
        try {
            // PURE ALLOCATION SYSTEM: Always use allocations, never direct links
            $pembayaran->update([
                'tagihan_bulanan_id' => null,
                'tagihan_terjadwal_id' => null,
                'payment_type' => 'allocated',
                'total_allocations' => count($allocations)
            ]);

            // Create allocation records for ALL tagihan
            foreach ($allocations as $order => $allocation) {
                $tagihanId = $allocation['tagihan_id'];
                $type = $allocation['type'];
                $amount = $allocation['allocated_amount'];

                // CRITICAL: Skip if amount <= 0
                if ($amount <= 0) {
                    \Log::warning('Skipping zero allocation', [
                        'type' => $type,
                        'tagihan_id' => $tagihanId,
                        'amount' => $amount
                    ]);
                    continue;
                }

                // Create PaymentAllocation record
                \App\Models\PaymentAllocation::create([
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'tagihan_bulanan_id' => $type === 'bulanan' ? $tagihanId : null,
                    'tagihan_terjadwal_id' => $type === 'terjadwal' ? $tagihanId : null,
                    'allocated_amount' => $amount,
                    'allocation_order' => $order + 1
                ]);

                \Log::info('Allocation created', [
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'type' => $type,
                    'tagihan_id' => $tagihanId,
                    'amount' => $amount,
                    'order' => $order + 1
                ]);
            }

            // Update status untuk semua tagihan yang terdampak
            $this->updateAllTagihanStatus($allocations);

            \Log::info('Pure allocation processed successfully', [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'allocations_count' => count($allocations),
                'total_amount' => collect($allocations)->sum('allocated_amount')
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to process allocations', [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'error' => $e->getMessage(),
                'allocations' => $allocations
            ]);
            throw $e;
        }
    }

    /**
     * Void pembayaran
     */
    public function voidPayment(Pembayaran $pembayaran, string $reason)
    {
        DB::beginTransaction();

        try {
            // 1. Backup tagihan yang terkena dampak sebelum void
            $affectedTagihanBulanan = [];
            $affectedTagihanTerjadwal = [];

            // Collect tagihan dari direct payment
            if ($pembayaran->tagihan_bulanan_id) {
                $affectedTagihanBulanan[] = $pembayaran->tagihan_bulanan_id;
            }

            if ($pembayaran->tagihan_terjadwal_id) {
                $affectedTagihanTerjadwal[] = $pembayaran->tagihan_terjadwal_id;
            }

            // Collect tagihan dari payment allocations
            if ($pembayaran->payment_type === 'allocated') {
                foreach ($pembayaran->paymentAllocations as $allocation) {
                    if ($allocation->tagihan_bulanan_id) {
                        $affectedTagihanBulanan[] = $allocation->tagihan_bulanan_id;
                    }
                    if ($allocation->tagihan_terjadwal_id) {
                        $affectedTagihanTerjadwal[] = $allocation->tagihan_terjadwal_id;
                    }
                }
            }

            // 2. Void pembayaran
            $pembayaran->update([
                'is_void' => true,
                'voided_at' => now(),
                'voided_by' => auth()->id(),
                'void_reason' => $reason
            ]);

            // 3. Delete payment allocations jika ada
            if ($pembayaran->payment_type === 'allocated') {
                $pembayaran->paymentAllocations()->delete();
            }

            // 4. UPDATE STATUS TAGIHAN KEMBALI - INI YANG PENTING!

            // Update tagihan bulanan
            foreach (array_unique($affectedTagihanBulanan) as $tagihanId) {
                $tagihan = \App\Models\TagihanBulanan::find($tagihanId);
                if ($tagihan) {
                    $tagihan->updateStatus(); // Method yang sudah ada di model
                }
            }

            // Update tagihan terjadwal
            foreach (array_unique($affectedTagihanTerjadwal) as $tagihanId) {
                $tagihan = \App\Models\TagihanTerjadwal::find($tagihanId);
                if ($tagihan) {
                    // Hitung ulang status berdasarkan pembayaran yang tidak void
                    $totalPembayaran = $tagihan->pembayarans()
                        ->where('is_void', false)
                        ->sum('nominal_pembayaran');

                    $totalAlokasi = \App\Models\PaymentAllocation::whereHas('pembayaran', function ($q) {
                        $q->where('is_void', false);
                    })->where('tagihan_terjadwal_id', $tagihanId)
                        ->sum('allocated_amount');

                    $totalDibayar = $totalPembayaran + $totalAlokasi;

                    if ($totalDibayar == 0) {
                        $status = 'belum_lunas';
                    } elseif ($totalDibayar >= $tagihan->nominal) {
                        $status = 'lunas';
                    } else {
                        $status = 'dibayar_sebagian';
                    }

                    $tagihan->update(['status' => $status]);
                }
            }

            DB::commit();

            \Log::info('Payment voided successfully', [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'affected_bulanan' => $affectedTagihanBulanan,
                'affected_terjadwal' => $affectedTagihanTerjadwal
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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

    protected function updateAllTagihanStatus(array $allocations): void
    {
        foreach ($allocations as $allocation) {
            try {
                $this->updateSingleTagihanStatus($allocation['type'], $allocation['tagihan_id']);
            } catch (\Exception $e) {
                \Log::warning('Failed to update tagihan status', [
                    'type' => $allocation['type'],
                    'tagihan_id' => $allocation['tagihan_id'],
                    'error' => $e->getMessage()
                ]);
                // Don't throw - continue with other tagihan
            }
        }
    }

    protected function updateSingleTagihanStatus(string $type, int $tagihanId): void
    {
        if ($type === 'bulanan') {
            $tagihanModel = \App\Models\TagihanBulanan::find($tagihanId);
            if ($tagihanModel) {
                // Clear cache untuk fresh calculation
                $tagihanModel->unsetRelation('pembayarans');
                $tagihanModel->unsetRelation('paymentAllocations');
                $tagihanModel->refresh();

                $oldStatus = $tagihanModel->status;
                $tagihanModel->updateStatus();
                $tagihanModel->refresh();

                \Log::info('Tagihan bulanan status updated', [
                    'id' => $tagihanId,
                    'old_status' => $oldStatus,
                    'new_status' => $tagihanModel->status,
                    'total_pembayaran' => $tagihanModel->total_pembayaran,
                    'sisa_tagihan' => $tagihanModel->sisa_tagihan
                ]);
            }
        } else {
            $tagihanModel = \App\Models\TagihanTerjadwal::find($tagihanId);
            if ($tagihanModel) {
                // Clear cache untuk fresh calculation
                $tagihanModel->unsetRelation('pembayarans');
                $tagihanModel->refresh();

                $oldStatus = $tagihanModel->status;
                $newStatus = $tagihanModel->calculateStatus();
                $tagihanModel->update(['status' => $newStatus]);
                $tagihanModel->refresh();

                \Log::info('Tagihan terjadwal status updated', [
                    'id' => $tagihanId,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'total_pembayaran' => $tagihanModel->total_pembayaran,
                    'sisa_tagihan' => $tagihanModel->sisa_tagihan
                ]);
            }
        }
    }
}


