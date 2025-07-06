<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranVoidController extends Controller
{
    /**
     * Show void confirmation page
     */
    public function show($id)
    {
        $this->authorize('pembayaran.void');

        $pembayaran = Pembayaran::with(['tagihanBulanan.santri', 'tagihanTerjadwal.santri', 'createdBy'])
            ->findOrFail($id);

        if (!$pembayaran->can_void) {
            return redirect()->back()->with('error', 'Pembayaran tidak dapat di-void (sudah lebih dari 24 jam atau sudah void)');
        }

        return view('pembayaran.void-confirmation', compact('pembayaran'));
    }

    /**
     * Get void modal content
     */
    public function voidModal($id)
    {
        $this->authorize('pembayaran.void');

        $pembayaran = Pembayaran::findOrFail($id);

        if (!$pembayaran->can_void) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak dapat di-void'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pembayaran->id_pembayaran,
                'receipt_number' => $pembayaran->receipt_number,
                'nominal' => $pembayaran->formatted_nominal,
                'santri_name' => $pembayaran->santri_name,
                'tanggal' => $pembayaran->tanggal_pembayaran->format('d/m/Y')
            ]
        ]);
    }

    /**
     * Process void pembayaran
     */
    public function void(Request $request, $id)
    {
        $this->authorize('pembayaran.void');

        $request->validate([
            'void_reason' => 'required|string|max:500'
        ], [
            'void_reason.required' => 'Alasan void harus diisi',
            'void_reason.max' => 'Alasan void maksimal 500 karakter'
        ]);

        DB::beginTransaction();

        try {
            $pembayaran = Pembayaran::findOrFail($id);

            // Double check if can void
            if (!$pembayaran->can_void) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak dapat di-void (sudah lebih dari 24 jam atau sudah void)'
                ], 422);
            }

            // Store santri ID for cache clearing
            $santriId = null;
            if ($pembayaran->tagihanBulanan && $pembayaran->tagihanBulanan->santri) {
                $santriId = $pembayaran->tagihanBulanan->santri_id;
            } elseif ($pembayaran->tagihanTerjadwal && $pembayaran->tagihanTerjadwal->santri) {
                $santriId = $pembayaran->tagihanTerjadwal->santri_id;
            }

            // Void the payment using model method
            $pembayaran->void($request->void_reason, auth()->id());

            // Clear santri tunggakan cache if santri found
            if ($santriId) {
                \Cache::forget("santri_tunggakan_{$santriId}");
            }

            DB::commit();

            Log::info('Pembayaran void successful', [
                'pembayaran_id' => $id,
                'voided_by' => auth()->user()->name,
                'reason' => $request->void_reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil di-void'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error voiding payment', [
                'pembayaran_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
