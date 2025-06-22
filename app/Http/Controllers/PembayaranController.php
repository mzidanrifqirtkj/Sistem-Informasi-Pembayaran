<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\Pembayaran;
use App\Services\PaymentService;
use App\Services\PaymentValidationService;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    protected $paymentService;
    protected $validationService;

    public function __construct(
        PaymentService $paymentService,
        PaymentValidationService $validationService
    ) {
        $this->paymentService = $paymentService;
        $this->validationService = $validationService;
    }

    /**
     * Display listing of santri untuk pembayaran
     */
    public function index(Request $request)
    {
        $query = Santri::with('kategoriSantri')
            ->where('status', 'aktif');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                    ->orWhere('nama_santri', 'like', "%{$search}%");
            });
        }

        // Filter by kategori
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori_santri_id', $request->kategori);
        }

        $santris = $query->orderBy('nama_santri')->paginate(20);

        return view('pembayaran.index', compact('santris'));
    }

    /**
     * Show payment form untuk santri
     */
    public function show($santriId)
    {
        try {
            $santri = Santri::findOrFail($santriId);

            // Validate santri aktif
            $this->validationService->validateSantriActive($santri);

            // Get tagihan data
            $data = $this->paymentService->getTagihanSantri($santri);

            return view('pembayaran.create', $data);

        } catch (\Exception $e) {
            return redirect()
                ->route('pembayaran.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Preview payment allocation
     */
    public function preview(Request $request)
    {
        try {
            \Log::info('Preview request received', $request->all());

            $request->validate([
                'santri_id' => 'required|exists:santris,id_santri',
                'nominal_pembayaran' => 'required|numeric|min:1',
                'selected_tagihan' => 'nullable|array',
                'selected_tagihan.*.type' => 'required_with:selected_tagihan|in:bulanan,terjadwal',
                'selected_tagihan.*.id' => 'required_with:selected_tagihan|integer',
                'selected_tagihan.*.sisa' => 'required_with:selected_tagihan|numeric'
            ]);

            $previewData = $this->paymentService->previewPaymentAllocation(
                $request->santri_id,
                $request->nominal_pembayaran,
                $request->selected_tagihan ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $previewData,
                'html' => view('pembayaran.preview-modal', $previewData)->render()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in preview', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . collect($e->errors())->flatten()->first()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Preview error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Store pembayaran
     */
    public function store(StorePaymentRequest $request)
    {
        try {
            DB::beginTransaction();

            // Parse payment data from JSON if exists
            $data = $request->has('payment_data')
                ? json_decode($request->payment_data, true)
                : $request->validated();

            // Process payment
            $pembayaran = $this->paymentService->processPayment($data);

            DB::commit();

            return redirect()
                ->route('pembayaran.receipt', $pembayaran->id_pembayaran)
                ->with('success', 'Pembayaran berhasil diproses');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment store error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show receipt/kwitansi
     */
    public function receipt($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihanBulanan.santri',
            'tagihanTerjadwal.santri',
            'paymentAllocations.tagihanBulanan',
            'paymentAllocations.tagihanTerjadwal',
            'createdBy'
        ])->findOrFail($id);

        $isReprint = request()->has('reprint');

        // Log reprint if needed
        if ($isReprint) {
            Log::info('Kwitansi reprint', [
                'pembayaran_id' => $id,
                'printed_by' => auth()->user()->name
            ]);
        }

        return view('pembayaran.receipt', compact('pembayaran', 'isReprint'));
    }

    /**
     * Print receipt
     */
    public function printReceipt($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihanBulanan.santri',
            'tagihanTerjadwal.santri',
            'paymentAllocations.tagihanBulanan',
            'paymentAllocations.tagihanTerjadwal'
        ])->findOrFail($id);

        $isReprint = request()->has('reprint');

        return view('pembayaran.print-receipt', compact('pembayaran', 'isReprint'));
    }

    /**
     * History pembayaran
     */
    public function history(Request $request)
    {
        $query = Pembayaran::with([
            'tagihanBulanan.santri',
            'tagihanTerjadwal.santri',
            'createdBy',
            'voidedBy'
        ])->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_pembayaran', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status == 'void') {
                $query->where('is_void', true);
            } elseif ($request->status == 'active') {
                $query->where('is_void', false);
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas('tagihanBulanan.santri', function ($q2) use ($search) {
                        $q2->where('nama_santri', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    })
                    ->orWhereHas('tagihanTerjadwal.santri', function ($q2) use ($search) {
                        $q2->where('nama_santri', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            });
        }

        $pembayarans = $query->paginate(20);

        return view('pembayaran.history', compact('pembayarans'));
    }
}
