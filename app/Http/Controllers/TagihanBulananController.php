<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\TagihanBulanan;
use App\Models\BiayaSantri;
use App\Models\Kelas;
use App\Models\TahunAjar;
use App\Models\GenerateLog;
use App\Models\Pembayaran;
use App\Exports\TagihanBulananExport;
use App\Services\PaymentAllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class TagihanBulananController extends Controller
{
    protected $paymentAllocationService;

    public function __construct(PaymentAllocationService $paymentAllocationService)
    {
        $this->paymentAllocationService = $paymentAllocationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', TagihanBulanan::class);

        $user = auth()->user();
        $filters = $request->only(['nama_santri', 'kelas_id', 'tahun', 'status', 'bulan']);
        $tahun = $filters['tahun'] ?? Carbon::now()->year;

        $availableYears = TagihanBulanan::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYears->isEmpty()) {
            $availableYears = collect([Carbon::now()->year]);
        }

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $stats = Cache::remember("dashboard_stats_{$tahun}", 300, function () use ($tahun) {
            return [
                'total_tagihan' => TagihanBulanan::where('tahun', $tahun)->count(),
                'total_lunas' => TagihanBulanan::where('tahun', $tahun)->where('status', 'lunas')->count(),
                'total_sebagian' => TagihanBulanan::where('tahun', $tahun)->where('status', 'dibayar_sebagian')->count(),
                'total_belum' => TagihanBulanan::where('tahun', $tahun)->where('status', 'belum_lunas')->count(),
                'total_nominal' => TagihanBulanan::where('tahun', $tahun)->sum('nominal'),
                'total_dibayar' => DB::table('tagihan_bulanans as tb')
                    ->leftJoin('pembayarans as p', 'tb.id_tagihan_bulanan', '=', 'p.tagihan_bulanan_id')
                    ->leftJoin('payment_allocations as pa', 'tb.id_tagihan_bulanan', '=', 'pa.tagihan_bulanan_id')
                    ->where('tb.tahun', $tahun)
                    ->whereNull('tb.deleted_at')
                    ->sum(DB::raw('COALESCE(p.nominal_pembayaran, 0) + COALESCE(pa.allocated_amount, 0)')),
            ];
        });

        $stats['total_kekurangan'] = $stats['total_nominal'] - $stats['total_dibayar'];
        $stats['collection_rate'] = $stats['total_nominal'] > 0
            ? round(($stats['total_dibayar'] / $stats['total_nominal']) * 100, 2)
            : 0;

        $query = Santri::with([
            'riwayatKelas.mapelKelas.kelas',
            'tagihanBulanan' => function ($q) use ($tahun, $filters) {
                $q->where('tahun', $tahun);
                if (!empty($filters['bulan'])) {
                    $q->where('bulan', $filters['bulan']);
                }
                if (!empty($filters['status'])) {
                    $q->where('status', $filters['status']);
                }
                $q->orderBy('bulan_urutan');
            },
            'tagihanBulanan.pembayarans',
            'tagihanBulanan.paymentAllocations'
        ]);

        if ($user->hasRole('santri')) {
            $query->where('id_santri', $user->santri->id_santri);
        } elseif ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            $santriIds = [$user->santri->id_santri];
            $taughtSantriIds = $this->getSantriIdsUstadzTeaches($user->santri);
            $santriIds = array_merge($santriIds, $taughtSantriIds);
            $query->whereIn('id_santri', array_unique($santriIds));
        }

        if (!empty($filters['nama_santri'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama_santri', 'like', '%' . $filters['nama_santri'] . '%')
                    ->orWhere('nis', 'like', '%' . $filters['nama_santri'] . '%');
            });
        }

        if (!empty($filters['kelas_id'])) {
            if ($filters['kelas_id'] === 'tanpa_kelas') {
                $query->whereDoesntHave('riwayatKelas');
            } else {
                $query->whereHas('riwayatKelas.mapelKelas.kelas', function ($q) use ($filters) {
                    $q->where('id_kelas', $filters['kelas_id']);
                });
            }
        }

        if (!empty($filters['status'])) {
            $query->whereHas('tagihanBulanan', function ($q) use ($tahun, $filters) {
                $q->where('tahun', $tahun)->where('status', $filters['status']);
            });
        }

        $santris = $query->where('status', 'aktif')->orderBy('nama_santri')->paginate(10);

        $santris->each(function ($santri) use ($tahun) {
            $santri->kelas_aktif = $santri->kelasAktif;
            $tagihans = $santri->tagihanBulanan->where('tahun', $tahun);
            $santri->total_tagihan = $tagihans->count();
            $santri->total_lunas = $tagihans->where('status', 'lunas')->count();
            $santri->total_sebagian = $tagihans->where('status', 'dibayar_sebagian')->count();
            $santri->total_belum = $tagihans->where('status', 'belum_lunas')->count();
            $santri->total_nominal = $tagihans->sum('nominal');
            $santri->total_dibayar = $tagihans->sum('total_pembayaran');
            $santri->total_kekurangan = $santri->total_nominal - $santri->total_dibayar;

            $monthlyStatus = [];
            foreach (TagihanBulanan::$bulanMapping as $bulan => $urutan) {
                $tagihan = $tagihans->firstWhere('bulan', $bulan);
                $monthlyStatus[$bulan] = $tagihan ? $tagihan->status : null;
            }
            $santri->monthly_status = $monthlyStatus;
        });

        if ($request->ajax()) {
            return response()->json([
                'santris' => $santris,
                'stats' => $stats
            ]);
        }

        return view('tagihan_bulanan.index', compact(
            'santris',
            'filters',
            'availableYears',
            'kelasList',
            'stats',
            'tahun'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', TagihanBulanan::class);

        $user = auth()->user();
        $query = Santri::where('status', 'aktif')->orderBy('nama_santri');

        // Role-based filtering for santri selection
        if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            // Ustadz can create for: own data + santri they teach
            $santriIds = [$user->santri->id_santri]; // Own data
            $taughtSantriIds = $this->getSantriIdsUstadzTeaches($user->santri);
            $santriIds = array_merge($santriIds, $taughtSantriIds);
            $query->whereIn('id_santri', array_unique($santriIds));
        }

        $santris = $query->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $months = TagihanBulanan::$bulanMapping;

        return view('tagihan_bulanan.create', compact('santris', 'years', 'months'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TagihanBulanan::class);

        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri',
            'bulan' => 'required|in:Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec',
            'tahun' => 'required|integer|min:2020|max:' . (Carbon::now()->year + 1),
            'nominal' => 'nullable|numeric|min:0',
            'custom_rincian' => 'nullable|array',
            'custom_rincian.*.nama' => 'required_with:custom_rincian|string',
            'custom_rincian.*.nominal' => 'required_with:custom_rincian|numeric|min:0'
        ]);

        // Check access to santri
        $this->checkSantriAccess($request->santri_id);

        // Check for duplicate
        $exists = TagihanBulanan::where('santri_id', $request->santri_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tagihan untuk santri, bulan, dan tahun tersebut sudah ada.')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $santri = Santri::findOrFail($request->santri_id);

            // Calculate rincian and nominal
            if ($request->has('custom_rincian') && !empty($request->custom_rincian)) {
                // Use custom rincian
                $rincian = $request->custom_rincian;
                $nominal = collect($rincian)->sum('nominal');
            } else {
                // Get from BiayaSantri
                $biayaSantris = BiayaSantri::where('santri_id', $santri->id_santri)
                    ->with('daftarBiaya.kategoriBiaya')
                    ->whereHas('daftarBiaya.kategoriBiaya', function ($q) {
                        $q->whereIn('status', ['tambahan', 'jalur']);
                    })
                    ->get();

                if ($biayaSantris->isEmpty()) {
                    return back()->with('error', 'Santri tidak memiliki alokasi biaya kategori tambahan/jalur.')
                        ->withInput();
                }

                $rincian = $biayaSantris->map(function ($biayaSantri) {
                    return [
                        'biaya_santri_id' => $biayaSantri->id_biaya_santri,
                        'nama' => $biayaSantri->daftarBiaya->kategoriBiaya->nama_kategori ?? null,
                        'nominal' => $biayaSantri->daftarBiaya->nominal
                    ];
                })->toArray();

                $nominal = $biayaSantris->sum(function ($item) {
                    return $item->daftarBiaya->nominal;
                });
            }

            // Override nominal if provided
            if ($request->filled('nominal')) {
                $nominal = $request->nominal;
            }

            // Create tagihan
            $tagihan = TagihanBulanan::create([
                'santri_id' => $santri->id_santri,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'nominal' => $nominal,
                'rincian' => $rincian,
                'status' => 'belum_lunas'
            ]);

            DB::commit();

            return redirect()->route('tagihan_bulanan.index')
                ->with('success', 'Tagihan bulanan berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tagihan bulanan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat membuat tagihan.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id, $tahun = null)
    {
        $this->authorize('tagihan-bulanan.view');

        // $id adalah santri_id, bukan tagihan_id
        $santri = Santri::with([
            'riwayatKelas.mapelKelas.kelas',
            'biayaSantris.daftarBiaya.kategoriBiaya'
        ])->findOrFail($id);

        // Check access to santri
        $this->checkSantriAccess($santri->id_santri);

        // Get tahun from parameter or current year
        $tahun = $tahun ?? request('tahun') ?? Carbon::now()->year;

        // Get all tagihan for this santri in the specified year
        $tagihanBulanan = TagihanBulanan::where('santri_id', $santri->id_santri)
            ->where('tahun', $tahun)
            ->with(['pembayarans', 'paymentAllocations.pembayaran'])
            ->get();

        // Get available years for this santri
        $availableYears = TagihanBulanan::where('santri_id', $santri->id_santri)
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([Carbon::now()->year]);
        }

        // Create monthly structure
        $allMonths = [
            'Jan' => 'Januari',
            'Feb' => 'Februari',
            'Mar' => 'Maret',
            'Apr' => 'April',
            'May' => 'Mei',
            'Jun' => 'Juni',
            'Jul' => 'Juli',
            'Aug' => 'Agustus',
            'Sep' => 'September',
            'Oct' => 'Oktober',
            'Nov' => 'November',
            'Dec' => 'Desember'
        ];

        // Group tagihan by month
        $tagihansByMonth = $tagihanBulanan->keyBy('bulan');

        // Process each tagihan to add calculated fields
        $tagihansByMonth->each(function ($tagihan) {
            if ($tagihan) {
                // Calculate total pembayaran
                $totalPembayaran = $tagihan->pembayarans->sum('nominal_pembayaran');
                $totalAllocation = $tagihan->paymentAllocations->sum('allocated_amount');
                $tagihan->total_pembayaran = $totalPembayaran + $totalAllocation;
                $tagihan->sisa_tagihan = $tagihan->nominal - $tagihan->total_pembayaran;

                // Add status color
                $tagihan->status_color = match ($tagihan->status) {
                    'lunas' => 'success',
                    'dibayar_sebagian' => 'warning',
                    'belum_lunas' => 'danger',
                    default => 'secondary'
                };
            }
        });

        // Add tagihan collection to santri for summary calculations
        $santri->tagihanBulanan = $tagihanBulanan;

        // Get santri's current class name
        $santri->nama_kelas_aktif = $santri->kelasAktif->nama_kelas ?? 'Tanpa Kelas';

        return view('tagihan_bulanan.show', compact(
            'santri',
            'tahun',
            'allMonths',
            'tagihansByMonth',
            'availableYears'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->authorize('update', $id);

        $tagihan = TagihanBulanan::with('santri')->findOrFail($id);

        // Check access to santri
        $this->checkSantriAccess($tagihan->santri_id);

        // Check if can edit
        if (!$tagihan->canEdit()) {
            return redirect()->route('tagihan_bulanan.index')
                ->with('error', 'Tagihan yang sudah memiliki pembayaran tidak dapat diedit.');
        }

        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $months = TagihanBulanan::$bulanMapping;

        return view('tagihan_bulanan.edit', compact('tagihan', 'years', 'months'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', $id);

        $tagihan = TagihanBulanan::findOrFail($id);

        // Check access to santri
        $this->checkSantriAccess($tagihan->santri_id);

        // Check if can edit
        if (!$tagihan->canEdit()) {
            return redirect()->route('tagihan_bulanan.index')
                ->with('error', 'Tagihan yang sudah memiliki pembayaran tidak dapat diedit.');
        }

        $request->validate([
            'nominal' => 'required|numeric|min:0',
            'custom_rincian' => 'nullable|array',
            'custom_rincian.*.nama' => 'required_with:custom_rincian|string',
            'custom_rincian.*.nominal' => 'required_with:custom_rincian|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Update rincian if provided
            if ($request->has('custom_rincian') && !empty($request->custom_rincian)) {
                $tagihan->rincian = $request->custom_rincian;
            }

            $tagihan->nominal = $request->nominal;
            $tagihan->save();

            DB::commit();

            return redirect()->route('tagihan_bulanan.index')
                ->with('success', 'Tagihan bulanan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating tagihan bulanan', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memperbarui tagihan.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete', $id);

        $tagihan = TagihanBulanan::findOrFail($id);

        // Check access to santri
        $this->checkSantriAccess($tagihan->santri_id);

        // Check if can delete
        if (!$tagihan->canDelete()) {
            return redirect()->route('tagihan_bulanan.index')
                ->with('error', 'Tagihan yang sudah memiliki pembayaran tidak dapat dihapus.');
        }

        try {
            $tagihan->delete();

            return redirect()->route('tagihan_bulanan.index')
                ->with('success', 'Tagihan bulanan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error deleting tagihan bulanan', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus tagihan.');
        }
    }

    /**
     * Show form for bulk generation
     */
    public function createBulkBulanan(Request $request)
    {
        $this->authorize('tagihan-bulanan.create');

        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $months = TagihanBulanan::$bulanMapping;

        // Get kelas list
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $user = auth()->user();
        $query = Santri::where('status', 'aktif')->with('riwayatKelas.mapelKelas.kelas')->orderBy('nama_santri');

        // Role-based filtering for santri selection
        if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            // Ustadz can create for: own data + santri they teach
            $santriIds = [$user->santri->id_santri]; // Own data
            $taughtSantriIds = $this->getSantriIdsUstadzTeaches($user->santri);
            $santriIds = array_merge($santriIds, $taughtSantriIds);
            $query->whereIn('id_santri', array_unique($santriIds));
        }

        $santris = $query->get();

        return view('tagihan_bulanan.createBulk', compact('years', 'months', 'kelasList', 'santris'));
    }

    /**
     * Generate bulk tagihan bulanan
     */
    public function generateBulkBulanan(Request $request)
    {
        $this->authorize('tagihan-bulanan.create');

        $request->validate([
            'tahun' => 'required|integer|min:2020|max:' . (Carbon::now()->year + 1),
            'bulan' => 'required|array|min:1',
            'bulan.*' => 'in:Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec',
            'santri_ids' => 'nullable|array',
            'santri_ids.*' => 'exists:santris,id_santri',
            'kelas_id' => 'nullable'
        ]);

        // Check lock to prevent concurrent generation
        $lockKey = 'bulk_generate_tagihan_bulanan_' . auth()->id();
        if (Cache::has($lockKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Proses generate sedang berjalan. Harap tunggu.'
            ], 409);
        }

        // Set lock for 10 minutes
        Cache::put($lockKey, true, 600);

        DB::beginTransaction();
        try {
            // Start generate log
            $generateLog = GenerateLog::startLog('bulk_tagihan_bulanan', [
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'santri_ids' => $request->santri_ids,
                'kelas_id' => $request->kelas_id,
                'user' => auth()->user()->name
            ]);

            // Build santri query
            $query = Santri::where('status', 'aktif');

            $user = auth()->user();

            // Role-based filtering
            if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
                // Ustadz can generate for: own data + santri they teach
                $allowedSantriIds = [$user->santri->id_santri]; // Own data
                $taughtSantriIds = $this->getSantriIdsUstadzTeaches($user->santri);
                $allowedSantriIds = array_merge($allowedSantriIds, $taughtSantriIds);
                $query->whereIn('id_santri', array_unique($allowedSantriIds));
            }

            // Filter by selected santri IDs if provided
            if (!empty($request->santri_ids)) {
                $query->whereIn('id_santri', $request->santri_ids);
            }
            // Or filter by kelas
            elseif (!empty($request->kelas_id)) {
                if ($request->kelas_id === 'tanpa_kelas') {
                    $query->whereDoesntHave('riwayatKelas');
                } else {
                    $query->whereHas('riwayatKelas.mapelKelas.kelas', function ($q) use ($request) {
                        $q->where('id_kelas', $request->kelas_id);
                    });
                }
            }

            // Get santri yang punya BiayaSantri kategori tambahan/jalur
            $santris = $query->whereHas('biayaSantris.daftarBiaya.kategoriBiaya', function ($q) {
                $q->whereIn('status', ['tambahan', 'jalur']);
            })->get();

            if ($santris->isEmpty()) {
                DB::rollBack();
                Cache::forget($lockKey);

                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada santri dengan alokasi biaya kategori tambahan/jalur.'
                ], 404);
            }

            $totalProcessed = 0;
            $totalSuccess = 0;
            $totalFailed = 0;
            $errors = [];
            $results = [];

            // Process each santri
            foreach ($santris as $santri) {
                // Get BiayaSantri untuk santri ini
                $biayaSantris = BiayaSantri::where('santri_id', $santri->id_santri)
                    ->with('daftarBiaya')
                    ->whereHas('daftarBiaya.kategoriBiaya', function ($q) {
                        $q->whereIn('status', ['tambahan', 'jalur']);
                    })
                    ->get();

                if ($biayaSantris->isEmpty()) {
                    continue; // Skip santri tanpa biaya
                }

                // Prepare rincian
                $rincian = $biayaSantris->map(function ($biayaSantri) {
                    return [
                        'biaya_santri_id' => $biayaSantri->id_biaya_santri,
                        'nama' => $biayaSantri->daftarBiaya->kategoriBiaya->nama_kategori ?? null,
                        'nominal' => $biayaSantri->daftarBiaya->nominal // ambil dari daftarBiaya->nominal
                    ];
                })->toArray();

                $nominal = $biayaSantris->sum(function ($item) {
                    return $item->daftarBiaya->nominal;
                });

                // Generate for each selected month
                foreach ($request->bulan as $bulan) {
                    $totalProcessed++;

                    try {
                        // Check if tagihan already exists
                        $exists = TagihanBulanan::where('santri_id', $santri->id_santri)
                            ->where('bulan', $bulan)
                            ->where('tahun', $request->tahun)
                            ->exists();

                        if ($exists) {
                            $errors[] = "Tagihan {$santri->nama_santri} untuk {$bulan} {$request->tahun} sudah ada";
                            continue;
                        }

                        // Create tagihan
                        $tagihan = TagihanBulanan::create([
                            'santri_id' => $santri->id_santri,
                            'bulan' => $bulan,
                            'tahun' => $request->tahun,
                            'nominal' => $nominal,
                            'rincian' => $rincian,
                            'status' => 'belum_lunas'
                        ]);

                        $totalSuccess++;
                        $generateLog->incrementSuccess();

                        $results[] = [
                            'santri' => $santri->nama_santri,
                            'bulan' => $bulan,
                            'nominal' => $nominal,
                            'status' => 'success'
                        ];

                    } catch (\Exception $e) {
                        $totalFailed++;
                        $error = "Error untuk {$santri->nama_santri} bulan {$bulan}: " . $e->getMessage();
                        $errors[] = $error;
                        $generateLog->incrementFailed($error);

                        Log::error('Error generating tagihan bulanan', [
                            'santri_id' => $santri->id_santri,
                            'bulan' => $bulan,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Update generate log
            $generateLog->update([
                'total_processed' => $totalProcessed,
                'total_success' => $totalSuccess,
                'total_failed' => $totalFailed,
                'errors' => $errors
            ]);
            $generateLog->finish();

            DB::commit();
            Cache::forget($lockKey);

            // Clear dashboard cache
            Cache::forget('dashboard_stats');
            Cache::forget("dashboard_stats_{$request->tahun}");

            return response()->json([
                'success' => true,
                'message' => "Generate tagihan selesai. {$totalSuccess} berhasil, {$totalFailed} gagal.",
                'processed' => $totalProcessed,
                'successful' => $totalSuccess,
                'failed' => $totalFailed,
                'errors' => $errors,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Cache::forget($lockKey);

            Log::error('Error in bulk generate tagihan bulanan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export tagihan bulanan to Excel
     */
    public function export(Request $request)
    {
        $this->authorize('tagihan-bulanan.export');

        $filters = $request->only(['nama_santri', 'kelas_id', 'tahun', 'status', 'bulan']);
        $tahun = $filters['tahun'] ?? Carbon::now()->year;

        $fileName = "Tagihan_Bulanan_{$tahun}.xlsx";

        if (!empty($filters['bulan'])) {
            $fileName = "Tagihan_Bulanan_{$filters['bulan']}_{$tahun}.xlsx";
        }

        return Excel::download(new TagihanBulananExport($filters), $fileName);
    }

    /**
     * Get available months for generate
     */
    public function getAvailableMonths(Request $request)
    {
        $this->authorize('tagihan-bulanan.view');

        $request->validate([
            'tahun' => 'required|integer',
            'santri_ids' => 'nullable|array'
        ]);

        $tahun = $request->tahun;
        $santriIds = $request->santri_ids ?? [];

        // Get existing tagihan for the year
        $query = TagihanBulanan::where('tahun', $tahun);

        if (!empty($santriIds)) {
            $query->whereIn('santri_id', $santriIds);
        }

        $existingMonths = $query->select('bulan', 'santri_id')
            ->get()
            ->groupBy('santri_id')
            ->map(function ($tagihans) {
                return $tagihans->pluck('bulan')->toArray();
            });

        // Calculate available months
        $allMonths = array_keys(TagihanBulanan::$bulanMapping);
        $availableMonths = [];

        foreach ($allMonths as $month) {
            $hasTagihan = false;

            if (empty($santriIds)) {
                // If no specific santri selected, check if any santri has tagihan
                $hasTagihan = TagihanBulanan::where('tahun', $tahun)
                    ->where('bulan', $month)
                    ->exists();
            } else {
                // Check if ALL selected santri already have tagihan for this month
                $santriWithTagihan = 0;
                foreach ($santriIds as $santriId) {
                    if (isset($existingMonths[$santriId]) && in_array($month, $existingMonths[$santriId])) {
                        $santriWithTagihan++;
                    }
                }
                $hasTagihan = ($santriWithTagihan == count($santriIds));
            }

            $availableMonths[] = [
                'month' => $month,
                'hasTagihan' => $hasTagihan,
                'available' => !$hasTagihan
            ];
        }

        return response()->json([
            'months' => $availableMonths,
            'tahun' => $tahun
        ]);
    }

    /**
     * Create payment for tagihan
     */
    public function createPayment(Request $request, $id)
    {
        $this->authorize('tagihan-bulanan.edit');

        $request->validate([
            'nominal_pembayaran' => 'required|numeric|min:1',
            'tanggal_pembayaran' => 'required|date'
        ]);

        $tagihan = TagihanBulanan::findOrFail($id);

        // Check access to santri
        $this->checkSantriAccess($tagihan->santri_id);

        if ($tagihan->status === 'lunas') {
            return back()->with('error', 'Tagihan sudah lunas.');
        }

        DB::beginTransaction();
        try {
            // Check for overpayment
            $sisaTagihan = $tagihan->sisa_tagihan;
            $isOverpayment = $request->nominal_pembayaran > $sisaTagihan;

            // Create pembayaran
            $pembayaran = Pembayaran::create([
                'tagihan_bulanan_id' => $tagihan->id_tagihan_bulanan,
                'nominal_pembayaran' => $request->nominal_pembayaran,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'created_by_id' => auth()->id()
            ]);

            // If overpayment, show options
            if ($isOverpayment) {
                $overpayment = $request->nominal_pembayaran - $sisaTagihan;

                session()->flash('overpayment', [
                    'amount' => $overpayment,
                    'pembayaran_id' => $pembayaran->id_pembayaran
                ]);
            }

            DB::commit();

            return redirect()->route('tagihan_bulanan.show', $id)
                ->with('success', 'Pembayaran berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating payment', [
                'tagihan_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat mencatat pembayaran.');
        }
    }

    /**
     * Handle overpayment
     */
    public function handleOverpayment(Request $request)
    {
        $this->authorize('tagihan-bulanan.edit');

        $request->validate([
            'pembayaran_id' => 'required|exists:pembayarans,id_pembayaran',
            'action' => 'required|in:refund,next_month'
        ]);

        // Implementation untuk handle overpayment
        // Bisa dikembangkan lebih lanjut sesuai kebutuhan

        return response()->json([
            'success' => true,
            'message' => 'Overpayment berhasil diproses.'
        ]);
    }

    /**
     * Get santri biaya info
     */
    public function getSantriBiayaInfo(Request $request)
    {
        $this->authorize('tagihan-bulanan.view');

        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri'
        ]);

        // Check access to santri
        $this->checkSantriAccess($request->santri_id);

        $santri = Santri::with(['biayaSantris.daftarBiaya.kategoriBiaya'])->findOrFail($request->santri_id);

        $biayaSantris = $santri->biayaSantris->filter(function ($biayaSantri) {
            return $biayaSantri->daftarBiaya &&
                $biayaSantri->daftarBiaya->kategoriBiaya &&
                in_array($biayaSantri->daftarBiaya->kategoriBiaya->status, ['tambahan', 'jalur']);
        });

        if ($biayaSantris->isEmpty()) {
            return response()->json([
                'success' => true,
                'rincian' => [],
                'total' => 0,
                'formatted_total' => 'Rp 0'
            ]);
        }

        $rincian = $biayaSantris->map(function ($biayaSantri) {
            return [
                'nama' => $biayaSantri->daftarBiaya->kategoriBiaya->nama_kategori ?? null,
                'nominal' => $biayaSantri->daftarBiaya->nominal // ambil dari daftarBiaya->nominal
            ];
        });

        $total = $biayaSantris->sum(function ($item) {
            return $item->daftarBiaya->nominal;
        });

        return response()->json([
            'success' => true,
            'rincian' => $rincian,
            'total' => $total,
            'formatted_total' => 'Rp ' . number_format($total, 0, ',', '.')
        ]);
    }

    /**
     * Dashboard view
     */
    public function dashboard(Request $request)
    {
        $this->authorize('tagihan-bulanan.view');

        $tahun = $request->get('tahun', Carbon::now()->year);

        // Get comprehensive stats
        $stats = Cache::remember("dashboard_complete_{$tahun}", 300, function () use ($tahun) {
            // Monthly collection data for chart
            $monthlyData = [];
            foreach (TagihanBulanan::$bulanMapping as $bulan => $urutan) {
                $tagihans = TagihanBulanan::where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->get();

                $monthlyData[] = [
                    'month' => $bulan,
                    'total_tagihan' => $tagihans->count(),
                    'nominal_tagihan' => $tagihans->sum('nominal'),
                    'total_lunas' => $tagihans->where('status', 'lunas')->count(),
                    'total_dibayar' => $tagihans->sum('total_pembayaran'),
                    'collection_rate' => $tagihans->sum('nominal') > 0
                        ? round(($tagihans->sum('total_pembayaran') / $tagihans->sum('nominal')) * 100, 2)
                        : 0
                ];
            }

            // Outstanding by class
            $outstandingByClass = DB::table('santris as s')
                ->leftJoin('tagihan_bulanans as tb', 's.id_santri', '=', 'tb.santri_id')
                ->leftJoin('riwayat_kelas as rk', function ($join) {
                    $join->on('s.id_santri', '=', 'rk.santri_id')
                        ->whereRaw('rk.id_riwayat_kelas = (
                             SELECT MAX(rk2.id_riwayat_kelas)
                             FROM riwayat_kelas rk2
                             WHERE rk2.santri_id = s.id_santri
                         )');
                })
                ->leftJoin('mapel_kelas as mk', 'rk.mapel_kelas_id', '=', 'mk.id_mapel_kelas')
                ->leftJoin('kelas as k', 'mk.kelas_id', '=', 'k.id_kelas')
                ->select(
                    DB::raw('COALESCE(k.nama_kelas, "Tanpa Kelas") as kelas'),
                    DB::raw('COUNT(DISTINCT tb.id_tagihan_bulanan) as total_tagihan'),
                    DB::raw('SUM(CASE WHEN tb.status = "belum_lunas" THEN 1 ELSE 0 END) as total_belum_lunas'),
                    DB::raw('SUM(CASE WHEN tb.status = "belum_lunas" THEN tb.nominal ELSE 0 END) as nominal_tunggakan')
                )
                ->where('tb.tahun', $tahun)
                ->whereNull('tb.deleted_at')
                ->groupBy('k.id_kelas', 'k.nama_kelas')
                ->get();

            return [
                'monthlyData' => $monthlyData,
                'outstandingByClass' => $outstandingByClass
            ];
        });

        return view('tagihan_bulanan.dashboard', compact('stats', 'tahun'));
    }

    /**
     * Get santri yearly tagihan data for create form
     */
    public function getSantriYearlyData(Request $request)
    {
        $this->authorize('tagihan-bulanan.view');

        // Debug log untuk melihat data yang diterima
        Log::info('getSantriYearlyData called', [
            'all_input' => $request->all(),
            'santri_id' => $request->input('santri_id'),
            'tahun' => $request->input('tahun'),
            'query_params' => $request->query(),
            'post_params' => $request->post()
        ]);

        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri',
            'tahun' => 'required|integer'
        ]);

        try {
            $santriId = $request->input('santri_id');
            $tahun = $request->input('tahun');

            // Check access to santri
            $this->checkSantriAccess($santriId);

            $santri = Santri::findOrFail($santriId);

            // Get all tagihan for the santri in the specified year
            $tagihans = TagihanBulanan::where('santri_id', $santri->id_santri)
                ->where('tahun', $tahun)
                ->with(['pembayarans', 'paymentAllocations'])
                ->get();

            Log::info('Tagihan query result', [
                'santri_id' => $santri->id_santri,
                'tahun' => $tahun,
                'tagihan_count' => $tagihans->count()
            ]);

            if ($tagihans->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Belum ada tagihan untuk santri ini di tahun ' . $tahun,
                    'data' => [],
                    'summary' => [
                        'total_tagihan' => 0,
                        'total_lunas' => 0,
                        'total_dibayar_sebagian' => 0,
                        'total_belum_lunas' => 0,
                        'total_nominal' => 0,
                        'total_dibayar' => 0,
                        'sisa_tagihan' => 0,
                        'lunas_percentage' => 0,
                        'belum_lunas_percentage' => 0
                    ]
                ]);
            }

            // Calculate summary
            $totalTagihan = $tagihans->count();
            $totalLunas = $tagihans->where('status', 'lunas')->count();
            $totalDibayarSebagian = $tagihans->where('status', 'dibayar_sebagian')->count();
            $totalBelumLunas = $tagihans->where('status', 'belum_lunas')->count();
            $totalNominal = $tagihans->sum('nominal');

            // Calculate total dibayar
            $totalDibayar = 0;
            foreach ($tagihans as $tagihan) {
                $pembayaranTotal = $tagihan->pembayarans ? $tagihan->pembayarans->sum('nominal_pembayaran') : 0;
                $allocationTotal = $tagihan->paymentAllocations ? $tagihan->paymentAllocations->sum('allocated_amount') : 0;
                $totalDibayar += ($pembayaranTotal + $allocationTotal);
            }

            $sisaTagihan = $totalNominal - $totalDibayar;

            $lunasPercentage = $totalTagihan > 0 ? round(($totalLunas / $totalTagihan) * 100, 1) : 0;
            $belumLunasPercentage = $totalTagihan > 0 ? round(($totalBelumLunas / $totalTagihan) * 100, 1) : 0;

            // Define month names
            $monthNames = [
                'Jan' => 'Januari',
                'Feb' => 'Februari',
                'Mar' => 'Maret',
                'Apr' => 'April',
                'May' => 'Mei',
                'Jun' => 'Juni',
                'Jul' => 'Juli',
                'Aug' => 'Agustus',
                'Sep' => 'September',
                'Oct' => 'Oktober',
                'Nov' => 'November',
                'Dec' => 'Desember'
            ];

            // Sort tagihan by month order
            $monthOrder = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $tagihans = $tagihans->sortBy(function ($tagihan) use ($monthOrder) {
                return array_search($tagihan->bulan, $monthOrder);
            });

            // Format data for table
            $data = $tagihans->map(function ($tagihan) use ($monthNames, $tahun) {
                $pembayaranTotal = $tagihan->pembayarans ? $tagihan->pembayarans->sum('nominal_pembayaran') : 0;
                $allocationTotal = $tagihan->paymentAllocations ? $tagihan->paymentAllocations->sum('allocated_amount') : 0;
                $totalDibayarTagihan = $pembayaranTotal + $allocationTotal;
                $sisaTagihan = $tagihan->nominal - $totalDibayarTagihan;

                return [
                    'id' => $tagihan->id_tagihan_bulanan,
                    'bulan' => $tagihan->bulan,
                    'bulan_text' => $monthNames[$tagihan->bulan] ?? $tagihan->bulan,
                    'tahun' => $tagihan->tahun,
                    'nominal' => $tagihan->nominal,
                    'total_dibayar' => $totalDibayarTagihan,
                    'sisa' => $sisaTagihan,
                    'status' => $tagihan->status,
                    'created_at' => $tagihan->created_at->format('d M Y'),
                    'detail_url' => route('tagihan_bulanan.show', ['id' => $tagihan->santri_id, 'tahun' => $tahun])
                ];
            })->values(); // Reset array keys

            return response()->json([
                'success' => true,
                'data' => $data,
                'summary' => [
                    'total_tagihan' => $totalTagihan,
                    'total_lunas' => $totalLunas,
                    'total_dibayar_sebagian' => $totalDibayarSebagian,
                    'total_belum_lunas' => $totalBelumLunas,
                    'total_nominal' => $totalNominal,
                    'total_dibayar' => $totalDibayar,
                    'sisa_tagihan' => $sisaTagihan,
                    'lunas_percentage' => $lunasPercentage,
                    'belum_lunas_percentage' => $belumLunasPercentage
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting santri yearly data', [
                'santri_id' => $request->input('santri_id'),
                'tahun' => $request->input('tahun'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has access to santri data
     */
    private function checkSantriAccess($santriId)
    {
        $user = auth()->user();

        // Admin has access to all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Santri can only access own data
        if ($user->hasRole('santri') && !$user->santri->is_ustadz) {
            if ($user->santri->id_santri != $santriId) {
                abort(403, 'Unauthorized access to santri data');
            }
            return true;
        }

        // Ustadz can access own data + santri they teach
        if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            // Own data
            if ($user->santri->id_santri == $santriId) {
                return true;
            }

            // Check if ustadz teaches this santri
            $santri = Santri::findOrFail($santriId);
            $santriPolicy = new \App\Policies\SantriPolicy();

            if ($santriPolicy->ustadzTeachesSantri($user, $santri)) {
                return true;
            }

            abort(403, 'Unauthorized access to santri data');
        }

        abort(403, 'Unauthorized access');
    }

    /**
     * Get santri IDs that ustadz teaches
     */
    private function getSantriIdsUstadzTeaches($ustadz)
    {
        $santriPolicy = new \App\Policies\SantriPolicy();
        $santriCollection = $santriPolicy->getSantriDiKelasUstadz($ustadz);

        return $santriCollection->pluck('id_santri')->toArray();
    }
}
