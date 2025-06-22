<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\TagihanBulanan;
use App\Models\Santri;
use Carbon\Carbon;

class PaymentValidationService
{
    /**
     * Validate payment amount
     */
    public function validatePaymentAmount($amount): bool
    {
        if ($amount <= 0) {
            throw new \Exception('Nominal pembayaran harus lebih dari 0');
        }

        return true;
    }

    /**
     * Check duplicate payment dalam window tertentu
     */
    public function validateDuplicatePayment($santriId, $nominalPembayaran, $timeThreshold = 5): void
    {
        $cutoffTime = now()->subMinutes($timeThreshold);

        $recentPayment = Pembayaran::where(function ($query) use ($santriId) {
            $query->whereHas('tagihanBulanan', function ($q) use ($santriId) {
                $q->where('santri_id', $santriId);
            })->orWhereHas('tagihanTerjadwal', function ($q) use ($santriId) {
                $q->where('santri_id', $santriId);
            });
        })
            ->where('nominal_pembayaran', $nominalPembayaran)
            ->where('created_at', '>=', $cutoffTime)
            ->where('is_void', false)
            ->exists();

        if ($recentPayment) {
            throw new \Exception('Pembayaran dengan nominal yang sama terdeteksi dalam 5 menit terakhir. Silakan periksa kembali atau tunggu beberapa saat.');
        }
    }

    /**
     * Validate monthly payment sequence (harus berurutan)
     */
    public function validateMonthlyPaymentSequence(Santri $santri, TagihanBulanan $tagihan): bool
    {
        // Cek apakah ada tagihan sebelumnya yang belum lunas
        $previousUnpaid = TagihanBulanan::where('santri_id', $santri->id_santri)
            ->where(function ($q) use ($tagihan) {
                $q->where('tahun', '<', $tagihan->tahun)
                    ->orWhere(function ($q2) use ($tagihan) {
                        $q2->where('tahun', $tagihan->tahun)
                            ->where('bulan_urutan', '<', $tagihan->bulan_urutan);
                    });
            })
            ->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
            ->exists();

        if ($previousUnpaid) {
            // Untuk auto allocation, skip tagihan ini
            // Untuk manual selection, throw error
            return false;
        }

        return true;
    }

    /**
     * Validate santri is active
     */
    public function validateSantriActive(Santri $santri): bool
    {
        if ($santri->status !== 'aktif') {
            throw new \Exception('Tidak dapat melakukan pembayaran untuk santri non-aktif');
        }

        return true;
    }

    /**
     * Validate payment date
     */
    public function validatePaymentDate($date): bool
    {
        $paymentDate = Carbon::parse($date);

        // Tidak boleh tanggal masa depan
        if ($paymentDate->isFuture()) {
            throw new \Exception('Tanggal pembayaran tidak boleh di masa depan');
        }

        // Tidak boleh lebih dari 30 hari ke belakang
        if ($paymentDate->lt(now()->subDays(30))) {
            throw new \Exception('Tanggal pembayaran tidak boleh lebih dari 30 hari yang lalu');
        }

        return true;
    }

    /**
     * Validate bulk payment data
     */
    public function validateBulkPayment(array $data): array
    {
        $errors = [];
        $validated = [];

        foreach ($data as $index => $row) {
            try {
                // Validate NIS exists
                $santri = Santri::where('nis', $row['nis'])->first();
                if (!$santri) {
                    throw new \Exception("NIS {$row['nis']} tidak ditemukan");
                }

                // Validate santri active
                $this->validateSantriActive($santri);

                // Validate amount
                $this->validatePaymentAmount($row['nominal']);

                // Validate date if provided
                if (isset($row['tanggal'])) {
                    $this->validatePaymentDate($row['tanggal']);
                }

                $validated[] = [
                    'santri' => $santri,
                    'nominal' => $row['nominal'],
                    'tanggal' => $row['tanggal'] ?? now(),
                    'keterangan' => $row['keterangan'] ?? null
                ];

            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $index + 1,
                    'nis' => $row['nis'] ?? 'N/A',
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'valid' => $validated,
            'errors' => $errors
        ];
    }

    /**
     * Validate void payment request
     */
    public function validateVoidRequest(Pembayaran $pembayaran): bool
    {
        // Check if already void
        if ($pembayaran->is_void) {
            throw new \Exception('Pembayaran sudah di-void sebelumnya');
        }

        // Check time limit (24 hours)
        if ($pembayaran->created_at->diffInHours(now()) > 24) {
            throw new \Exception('Pembayaran hanya dapat di-void dalam waktu 24 jam');
        }

        // Check user permission
        // if (!auth()->user()->hasRole('admin')) {
        if (!auth()->user()->can('pembayaran-void')) {
            throw new \Exception('Anda tidak memiliki izin untuk void pembayaran');
        }

        return true;
    }
}
