<?php

namespace App\Http\Controllers;

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

    public function index(Request $request)
    {
        $this->authorize('viewAny', Pembayaran::class);

        $user = auth()->user();
        $query = Santri::with('kategoriSantri')->where('status', 'aktif');

        if ($user->hasRole('santri') && !$user->santri->is_ustadz) {
            return redirect()->route('pembayaran.show', $user->santri->id_santri);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                    ->orWhere('nama_santri', 'like', "%{$search}%");
            });
        }

        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori_santri_id', $request->kategori);
        }

        $santris = $query->orderBy('nama_santri')->paginate(20);

        return view('pembayaran.index', compact('santris'));
    }

    public function show($santriId)
    {
        $this->authorize('create', Pembayaran::class);

        try {
            $santri = Santri::findOrFail($santriId);

            $user = auth()->user();
            if ($user->hasRole('santri') && !$user->santri->is_ustadz) {
                if ($user->santri->id_santri != $santri->id_santri) {
                    abort(403, 'Unauthorized access to payment data');
                }
            }

            $this->validationService->validateSantriActive($santri);
            $data = $this->paymentService->getTagihanSantri($santri);

            return view('pembayaran.create', $data);

        } catch (\Exception $e) {
            return redirect()->route('pembayaran.index')->with('error', $e->getMessage());
        }
    }

    public function preview(Request $request)
    {
        $this->authorize('create', Pembayaran::class);

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

    public function store(StorePaymentRequest $request)
    {
        $this->authorize('create', Pembayaran::class);

        try {
            DB::beginTransaction();

            \Log::info('Payment store request:', $request->all());

            $pembayaran = $this->paymentService->processPayment($request->validated());

            DB::commit();

            \Log::info('Payment processed successfully:', [
                'pembayaran_id' => $pembayaran->id_pembayaran,
                'receipt_number' => $pembayaran->receipt_number
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil diproses',
                    'pembayaran_id' => $pembayaran->id_pembayaran,
                    'redirect_url' => route('pembayaran.receipt', $pembayaran->id_pembayaran),
                    'should_refresh' => true,
                    'refresh_url' => route('pembayaran.show', $request->santri_id)
                ]);
            }

            return redirect()
                ->route('pembayaran.receipt', $pembayaran->id_pembayaran)
                ->with('success', 'Pembayaran berhasil diproses');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Payment store error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function receipt($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $this->authorize('view', $pembayaran);

        $pembayaran->load([
            'tagihanBulanan.santri',
            'tagihanTerjadwal.santri',
            'paymentAllocations.tagihanBulanan',
            'paymentAllocations.tagihanTerjadwal',
            'createdBy'
        ]);

        $user = auth()->user();
        if ($user->hasRole('santri') && !$user->santri->is_ustadz) {
            $santriId = $pembayaran->tagihanBulanan?->santri_id
                ?? $pembayaran->tagihanTerjadwal?->santri_id
                ?? $pembayaran->paymentAllocations->first()?->tagihanBulanan?->santri_id
                ?? $pembayaran->paymentAllocations->first()?->tagihanTerjadwal?->santri_id;

            if ($user->santri->id_santri != $santriId) {
                abort(403, 'Unauthorized access to payment receipt');
            }
        }

        $isReprint = request()->has('reprint');
        if ($isReprint) {
            Log::info('Kwitansi reprint', [
                'pembayaran_id' => $id,
                'printed_by' => auth()->user()->name
            ]);
        }

        return view('pembayaran.receipt', compact('pembayaran', 'isReprint'));
    }

    public function printReceipt($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $this->authorize('view', $pembayaran);

        $pembayaran->load([
            'tagihanBulanan.santri',
            'tagihanTerjadwal.santri',
            'paymentAllocations.tagihanBulanan',
            'paymentAllocations.tagihanTerjadwal'
        ]);

        $isReprint = request()->has('reprint');

        return view('pembayaran.print-receipt', compact('pembayaran', 'isReprint'));
    }

    public function history(Request $request)
    {
        $this->authorize('history', Pembayaran::class);

        $user = auth()->user();
        $query = Pembayaran::with([
            'tagihanBulanan.santri',
            'tagihanTerjadwal.santri',
            'paymentAllocations.tagihanBulanan.santri',
            'paymentAllocations.tagihanTerjadwal.santri',
            'createdBy',
            'voidedBy'
        ])->orderBy('created_at', 'desc');

        if ($user->hasRole('santri') && !$user->santri->is_ustadz) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('tagihanBulanan.santri', function ($sq) use ($user) {
                    $sq->where('id_santri', $user->santri->id_santri);
                })->orWhereHas('tagihanTerjadwal.santri', function ($sq) use ($user) {
                    $sq->where('id_santri', $user->santri->id_santri);
                })->orWhereHas('paymentAllocations.tagihanBulanan.santri', function ($sq) use ($user) {
                    $sq->where('id_santri', $user->santri->id_santri);
                })->orWhereHas('paymentAllocations.tagihanTerjadwal.santri', function ($sq) use ($user) {
                    $sq->where('id_santri', $user->santri->id_santri);
                });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_pembayaran', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('is_void', true);
            } elseif ($request->status === '0') {
                $query->where('is_void', false);
            }
        }

        if ($request->filled('search')) {
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
                    })
                    ->orWhereHas('paymentAllocations.tagihanBulanan.santri', function ($q2) use ($search) {
                        $q2->where('nama_santri', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    })
                    ->orWhereHas('paymentAllocations.tagihanTerjadwal.santri', function ($q2) use ($search) {
                        $q2->where('nama_santri', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            });
        }

        $pembayarans = $query->paginate(20);
        $pembayarans->appends($request->query());

        $totalHariIni = $query->get()->filter(fn($p) => $p->tanggal_pembayaran->isToday())
            ->sum(fn($p) => $p->paymentAllocations->sum('allocated_amount'));

        $totalBulanIni = $query->get()->filter(fn($p) => $p->tanggal_pembayaran->isSameMonth(now()))
            ->sum(fn($p) => $p->paymentAllocations->sum('allocated_amount'));

        $totalSemua = $query->get()->sum(fn($p) => $p->paymentAllocations->sum('allocated_amount'));

        return view('pembayaran.history', compact('pembayarans', 'totalHariIni', 'totalBulanIni', 'totalSemua'));
    }


}
