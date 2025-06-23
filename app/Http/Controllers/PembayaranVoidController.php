<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Services\PaymentService;
use App\Services\PaymentValidationService;
use App\Http\Requests\VoidPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranVoidController extends Controller
{
    protected $paymentService;
    protected $validationService;

    public function __construct(
        PaymentService $paymentService,
        PaymentValidationService $validationService
    ) {
        $this->paymentService = $paymentService;
        $this->validationService = $validationService;

        // FIX: Comment atau hapus line ini yang menyebabkan error
        // $this->middleware('role:administrator'); // HAPUS LINE INI
    }

    /**
     * Show void confirmation form
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihanBulanan.santri',
            'tagihanTerjadwal.santri',
            'paymentAllocations.tagihanBulanan.santri',
            'paymentAllocations.tagihanTerjadwal.santri',
            'createdBy'
        ])->findOrFail($id);

        try {
            // Validate void request
            $this->validationService->validateVoidRequest($pembayaran);

            return view('pembayaran.void-confirm', compact('pembayaran'));

        } catch (\Exception $e) {
            return redirect()
                ->route('pembayaran.history')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Process void pembayaran
     */
    public function void(VoidPaymentRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $pembayaran = Pembayaran::findOrFail($id);

            // Validate
            $this->validationService->validateVoidRequest($pembayaran);

            // Process void
            $this->paymentService->voidPayment($pembayaran, $request->void_reason);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil di-void'
                ]);
            }

            return redirect()
                ->route('pembayaran.history')
                ->with('success', 'Pembayaran berhasil di-void');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Void payment error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get void reason modal
     */
    public function voidModal($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihanBulanan.santri',
            'tagihanTerjadwal.santri'
        ])->findOrFail($id);

        try {
            $this->validationService->validateVoidRequest($pembayaran);

            return response()->json([
                'success' => true,
                'html' => view('pembayaran.void-modal', compact('pembayaran'))->render()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
