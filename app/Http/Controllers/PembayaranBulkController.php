<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\TagihanBulanan;
use App\Models\Pembayaran;
use App\Models\GenerateLog;
use App\Services\PaymentService;
use App\Services\PaymentValidationService;
use App\Services\PaymentImportService;
use App\Exports\PaymentTemplateExport;
use App\Imports\PaymentImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PembayaranBulkController extends Controller
{
    protected $paymentService;
    protected $validationService;
    protected $importService;

    public function __construct(
        PaymentService $paymentService,
        PaymentValidationService $validationService,
        PaymentImportService $importService
    ) {
        $this->paymentService = $paymentService;
        $this->validationService = $validationService;
        $this->importService = $importService;

        $this->middleware('role:administrator');
    }

    /**
     * Show bulk payment form
     */
    public function index(Request $request)
    {
        $query = Santri::with('kategoriSantri')
            ->where('status', 'aktif');

        // Filter by kategori
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori_santri_id', $request->kategori);
        }

        // Filter by kelas
        if ($request->has('kelas') && $request->kelas != '') {
            $query->where('kelas', $request->kelas);
        }

        $santris = $query->orderBy('nama_santri')->get();

        // Get available months for bulk payment
        $currentYear = date('Y');
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('M', mktime(0, 0, 0, $i, 1));
            $months[] = [
                'number' => $i,
                'name' => $monthName,
                'year' => $currentYear
            ];
        }

        return view('pembayaran.bulk', compact('santris', 'months'));
    }

    /**
     * Process bulk payment
     */
    public function process(Request $request)
    {
        $request->validate([
            'santri_ids' => 'required|array|min:1',
            'santri_ids.*' => 'exists:santris,id_santri',
            'payment_type' => 'required|in:same_amount,individual',
            'nominal_pembayaran' => 'required_if:payment_type,same_amount|numeric|min:1',
            'individual_amounts' => 'required_if:payment_type,individual|array',
            'individual_amounts.*' => 'numeric|min:1',
            'bulan' => 'nullable|required_if:for_month,true|in:Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec',
            'tahun' => 'nullable|required_if:for_month,true|integer|min:2020',
            'payment_note' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Start log
            $log = GenerateLog::startLog('bulk_payment', [
                'total_santri' => count($request->santri_ids),
                'payment_type' => $request->payment_type,
                'for_month' => $request->has('bulan') ? $request->bulan . ' ' . $request->tahun : 'auto'
            ]);

            $results = [
                'success' => [],
                'failed' => []
            ];

            foreach ($request->santri_ids as $index => $santriId) {
                try {
                    $santri = Santri::findOrFail($santriId);

                    // Validate santri active
                    $this->validationService->validateSantriActive($santri);

                    // Determine amount
                    $amount = $request->payment_type === 'same_amount'
                        ? $request->nominal_pembayaran
                        : ($request->individual_amounts[$santriId] ?? 0);

                    if ($amount <= 0) {
                        throw new \Exception('Nominal pembayaran harus lebih dari 0');
                    }

                    // Get tagihan data
                    $tagihanData = $this->paymentService->getTagihanSantri($santri);

                    // If specific month requested, filter tagihan
                    if ($request->has('bulan') && $request->has('tahun')) {
                        $tagihanData['tagihan_bulanan'] = $tagihanData['tagihan_bulanan']
                            ->where('bulan', $request->bulan)
                            ->where('tahun', $request->tahun);
                    }

                    // Preview allocation
                    $previewData = $this->paymentService->previewPaymentAllocation(
                        $santriId,
                        $amount,
                        [] // Auto allocation
                    );

                    // Process payment
                    $paymentData = [
                        'santri_id' => $santriId,
                        'nominal_pembayaran' => $amount,
                        'tanggal_pembayaran' => now(),
                        'payment_note' => $request->payment_note ?? 'Pembayaran Massal',
                        'allocations' => $previewData['allocations'],
                        'sisa_pembayaran' => $previewData['sisa_pembayaran']
                    ];

                    $pembayaran = $this->paymentService->processPayment($paymentData);

                    $results['success'][] = [
                        'santri' => $santri->nama_santri,
                        'nis' => $santri->nis,
                        'nominal' => $amount,
                        'receipt' => $pembayaran->receipt_number
                    ];

                    $log->incrementSuccess();

                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'santri' => $santri->nama_santri ?? 'Unknown',
                        'nis' => $santri->nis ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];

                    $log->incrementFailed($e->getMessage());
                }
            }

            $log->finish();
            DB::commit();

            // Prepare summary message
            $message = sprintf(
                'Pembayaran massal selesai. Berhasil: %d, Gagal: %d',
                count($results['success']),
                count($results['failed'])
            );

            return redirect()
                ->route('pembayaran.bulk.index')
                ->with('success', $message)
                ->with('bulk_results', $results);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk payment error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('pembayaran.import');
    }

    /**
     * Download import template
     */
    public function downloadTemplate(Request $request)
    {
        $type = $request->get('type', 'individual');

        return Excel::download(
            new PaymentTemplateExport($type),
            'template_pembayaran_' . $type . '_' . date('YmdHis') . '.xlsx'
        );
    }

    /**
     * Preview import data
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
            'import_type' => 'required|in:individual,bulk'
        ]);

        try {
            $file = $request->file('file');
            $importType = $request->import_type;

            // Parse file
            $data = $this->importService->parseFile($file, $importType);

            // Validate data
            $validated = $this->validationService->validateBulkPayment($data);

            // Store in session for processing
            session(['import_data' => $validated]);

            return response()->json([
                'success' => true,
                'valid_count' => count($validated['valid']),
                'error_count' => count($validated['errors']),
                'data' => $validated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Process import
     */
    public function import(Request $request)
    {
        try {
            DB::beginTransaction();

            // Get validated data from session
            $importData = session('import_data');
            if (!$importData) {
                throw new \Exception('Data import tidak ditemukan. Silakan upload ulang.');
            }

            // Start log
            $log = GenerateLog::startLog('import_pembayaran', [
                'total_rows' => count($importData['valid']),
                'import_date' => now()->toDateTimeString()
            ]);

            $results = [
                'success' => [],
                'failed' => []
            ];

            // Process each valid row
            foreach ($importData['valid'] as $row) {
                try {
                    // Get tagihan data
                    $tagihanData = $this->paymentService->getTagihanSantri($row['santri']);

                    // Preview allocation
                    $previewData = $this->paymentService->previewPaymentAllocation(
                        $row['santri']->id_santri,
                        $row['nominal'],
                        [] // Auto allocation
                    );

                    // Process payment
                    $paymentData = [
                        'santri_id' => $row['santri']->id_santri,
                        'nominal_pembayaran' => $row['nominal'],
                        'tanggal_pembayaran' => $row['tanggal'],
                        'payment_note' => $row['keterangan'] ?? 'Import Pembayaran',
                        'allocations' => $previewData['allocations'],
                        'sisa_pembayaran' => $previewData['sisa_pembayaran']
                    ];

                    $pembayaran = $this->paymentService->processPayment($paymentData);

                    $results['success'][] = [
                        'nis' => $row['santri']->nis,
                        'nama' => $row['santri']->nama_santri,
                        'nominal' => $row['nominal'],
                        'receipt' => $pembayaran->receipt_number
                    ];

                    $log->incrementSuccess();

                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'nis' => $row['santri']->nis ?? 'Unknown',
                        'nama' => $row['santri']->nama_santri ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];

                    $log->incrementFailed($e->getMessage());
                }
            }

            // Add pre-validation errors to failed
            foreach ($importData['errors'] as $error) {
                $results['failed'][] = $error;
            }

            $log->finish();
            DB::commit();

            // Clear session
            session()->forget('import_data');

            // Generate report
            $report = $this->importService->generateReport($results);

            return response()->json([
                'success' => true,
                'message' => sprintf(
                    'Import selesai. Berhasil: %d, Gagal: %d',
                    count($results['success']),
                    count($results['failed'])
                ),
                'report_url' => $report
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Import payment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
