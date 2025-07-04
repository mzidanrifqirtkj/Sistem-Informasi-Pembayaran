<?php

namespace App\Http\Controllers;

use App\Models\BiayaSantri;
use App\Models\DaftarBiaya;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\TagihanTerjadwal;
use App\Models\TahunAjar;
use App\Exports\TagihanTerjadwalExport;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class TagihanTerjadwalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', TagihanTerjadwal::class);

        $user = Auth::user();
        $query = TagihanTerjadwal::query();

        // Apply role-based filtering
        if ($user->hasRole('admin')) {
            $query->with(['santri', 'daftarBiaya.kategoriBiaya', 'biayaSantri', 'tahunAjar']);
        } elseif ($user->hasRole('santri') && !$user->santri->is_ustadz) {
            // Santri non-ustadz hanya lihat data sendiri
            $santri = $user->santri;
            if ($santri) {
                $query->where('santri_id', $santri->id_santri)
                    ->with(['santri', 'daftarBiaya.kategoriBiaya', 'biayaSantri', 'tahunAjar']);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            // Ustadz dapat melihat data sendiri + santri yang diajar
            $allowedSantriIds = [$user->santri->id_santri]; // Own data
            $taughtSantriIds = $this->getSantriIdsUstadzTeaches($user->santri);
            $allowedSantriIds = array_merge($allowedSantriIds, $taughtSantriIds);

            $query->whereIn('santri_id', array_unique($allowedSantriIds))
                ->with(['santri', 'daftarBiaya.kategoriBiaya', 'biayaSantri', 'tahunAjar']);
        } else {
            $query->whereRaw('1 = 0'); // No access
        }

        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jenis_biaya
        if ($request->filled('jenis_biaya')) {
            $query->whereHas('daftarBiaya.kategoriBiaya', function ($q) use ($request) {
                $q->where('id_kategori_biaya', $request->jenis_biaya);
            });
        }

        // Filter by jenis_kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('jenis_kelamin', $request->jenis_kelamin);
            });
        }

        // Filter by kelas_id (kelas terakhir dari riwayat_kelas)
        if ($request->filled('kelas_id')) {
            // Ambil riwayat_kelas terbaru berdasarkan tahun_ajar_id terbesar per santri
            $latestRiwayatIds = DB::table('riwayat_kelas as rk')
                ->select('rk.id_riwayat_kelas')
                ->join('mapel_kelas as mk', 'rk.mapel_kelas_id', '=', 'mk.id_mapel_kelas')
                ->whereIn('rk.id_riwayat_kelas', function ($sub) {
                    $sub->select(DB::raw('MAX(rk2.id_riwayat_kelas)'))
                        ->from('riwayat_kelas as rk2')
                        ->join('mapel_kelas as mk2', 'rk2.mapel_kelas_id', '=', 'mk2.id_mapel_kelas')
                        ->groupBy('rk2.santri_id');
                })
                ->where('mk.kelas_id', $request->kelas_id)
                ->pluck('rk.id_riwayat_kelas')
                ->toArray();

            // Terapkan ke query tagihan
            $query->whereHas('santri.riwayatKelas', function ($q) use ($latestRiwayatIds) {
                $q->whereIn('id_riwayat_kelas', $latestRiwayatIds);
            });
        }

        // Search by nama_santri or NIS
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('santri', function ($q) use ($searchTerm) {
                $q->where('nama_santri', 'like', "%{$searchTerm}%")
                    ->orWhere('nis', 'like', "%{$searchTerm}%");
            });
        }

        // Pagination per_page
        $perPage = $request->input('per_page', 10);
        $tagihanTerjadwals = $query->paginate($perPage)->appends($request->query());

        // Filter options
        $tahunOptions = TagihanTerjadwal::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $statusOptions = ['belum_lunas', 'dibayar_sebagian', 'lunas'];
        $jenisBiayaOptions = DaftarBiaya::with('kategoriBiaya')
            ->get()
            ->groupBy('kategoriBiaya.id_kategori_biaya')
            ->map(function ($items) {
                return $items->first()->kategoriBiaya;
            });

        $jenisKelaminOptions = [
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
        ];

        $kelasOptions = Kelas::orderBy('nama_kelas')->get();

        return view('tagihan-terjadwal.index', compact(
            'tagihanTerjadwals',
            'tahunOptions',
            'statusOptions',
            'jenisBiayaOptions',
            'jenisKelaminOptions',
            'kelasOptions'
        ));
    }

    public function create()
    {
        $this->authorize('create', TagihanTerjadwal::class);

        $user = auth()->user();
        $query = Santri::where('status', 'aktif');

        // Role-based filtering for santri selection
        if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            // Ustadz can create for: own data + santri they teach
            $allowedSantriIds = [$user->santri->id_santri]; // Own data
            $taughtSantriIds = $this->getSantriIdsUstadzTeaches($user->santri);
            $allowedSantriIds = array_merge($allowedSantriIds, $taughtSantriIds);
            $query->whereIn('id_santri', array_unique($allowedSantriIds));
        }

        $santris = $query->get();
        $tahunAjars = TahunAjar::all();

        return view('tagihan-terjadwal.create', compact('santris', 'tahunAjars'));
    }

    public function getBiayaSantriBySantriId(Request $request)
    {
        $this->authorize('tagihan-terjadwal.view');

        try {
            $santriId = $request->query('santri_id');

            if (!$santriId) {
                return response()->json(['error' => 'Santri ID is required'], 400);
            }

            // Check access to santri
            $this->checkSantriAccess($santriId);

            // Filter BiayaSantri yang memiliki KategoriBiaya dengan status 'tahunan' atau 'insidental'
            $biayaSantris = BiayaSantri::where('santri_id', $santriId)
                ->with('daftarBiaya.kategoriBiaya')
                ->whereHas('daftarBiaya.kategoriBiaya', function ($query) {
                    $query->whereIn('status', ['tahunan', 'insidental']);
                })
                ->get()
                ->map(function ($biayaSantri) {
                    // Validasi relasi untuk menghindari error
                    if (!$biayaSantri->daftarBiaya || !$biayaSantri->daftarBiaya->kategoriBiaya) {
                        Log::warning("BiayaSantri ID {$biayaSantri->id_biaya_santri} has broken relations");
                        return null;
                    }

                    $nominalGabungan = $biayaSantri->daftarBiaya->nominal * $biayaSantri->jumlah;
                    $statusKategori = $biayaSantri->daftarBiaya->kategoriBiaya->status;

                    return [
                        'id' => $biayaSantri->id_biaya_santri,
                        'nama_biaya_paket' => $biayaSantri->daftarBiaya->kategoriBiaya->nama_kategori .
                            ' [' . ucfirst($statusKategori) . ']' .
                            ' (Rp ' . number_format($nominalGabungan, 0, ',', '.') .
                            ($biayaSantri->jumlah > 1 ? ' x ' . $biayaSantri->jumlah : '') . ')',
                        'daftar_biaya_id' => $biayaSantri->daftarBiaya->id_daftar_biaya,
                        'nominal_tagihan_default' => $nominalGabungan,
                        'status_kategori' => $statusKategori,
                    ];
                })
                ->filter(); // Remove null values

            return response()->json($biayaSantris);
        } catch (Exception $e) {
            Log::error('Error in getBiayaSantriBySantriId: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data biaya santri'], 500);
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create', TagihanTerjadwal::class);

        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'santri_id' => 'required|exists:santris,id_santri',
                'biaya_santri_id' => 'required|exists:biaya_santris,id_biaya_santri',
                'daftar_biaya_id' => 'required|exists:daftar_biayas,id_daftar_biaya',
                'tahun' => 'required|integer|min:2000|max:' . (now()->year + 2),
                'tahun_ajar_id' => 'nullable|exists:tahun_ajars,id_tahun_ajar',
                'nominal' => 'required|numeric|min:0',
            ]);

            // Check access to santri
            $this->checkSantriAccess($validatedData['santri_id']);

            // Pengecekan duplikasi berdasarkan biaya_santri_id + tahun + tahun_ajar_id
            $existsQuery = TagihanTerjadwal::where('biaya_santri_id', $validatedData['biaya_santri_id'])
                ->where('tahun', $validatedData['tahun']);

            if ($validatedData['tahun_ajar_id']) {
                $existsQuery->where('tahun_ajar_id', $validatedData['tahun_ajar_id']);
            } else {
                $existsQuery->whereNull('tahun_ajar_id');
            }

            if ($existsQuery->exists()) {
                return back()->withInput()->withErrors([
                    'biaya_santri_id' => 'Tagihan untuk alokasi biaya, tahun, dan tahun ajar yang dipilih sudah ada.'
                ]);
            }

            $data = [
                'santri_id' => $validatedData['santri_id'],
                'daftar_biaya_id' => $validatedData['daftar_biaya_id'],
                'biaya_santri_id' => $validatedData['biaya_santri_id'],
                'nominal' => $validatedData['nominal'],
                'status' => 'belum_lunas',
                'tahun' => $validatedData['tahun'],
                'tahun_ajar_id' => $validatedData['tahun_ajar_id'],
                'rincian' => [
                    ['keterangan' => 'Tagihan manual', 'nominal' => $validatedData['nominal']]
                ],
            ];

            TagihanTerjadwal::create($data);

            DB::commit();
            return redirect()->route('tagihan_terjadwal.index')->with('success', 'Tagihan berhasil dibuat.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->errors());
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating tagihan: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }
    }

    public function createBulkTagihanTerjadwal()
    {
        $this->authorize('tagihan-terjadwal.create');

        // Ambil status unik dari KategoriBiaya, namun hanya tampilkan 'tahunan' dan 'insidental'
        $kategoriBiayaStatus = ['tahunan', 'insidental']; // Hardcode untuk membatasi pilihan

        // Ambil jenis biaya berdasarkan status yang difilter
        $jenisBiayaOptions = DaftarBiaya::with('kategoriBiaya')
            ->whereHas('kategoriBiaya', function ($query) use ($kategoriBiayaStatus) {
                $query->whereIn('status', $kategoriBiayaStatus);
            })
            ->get()
            ->groupBy('kategoriBiaya.id_kategori_biaya')
            ->map(function ($items) {
                return $items->first()->kategoriBiaya;
            })
            ->sortBy('nama_kategori');

        $tahunAjars = TahunAjar::all();
        $currentYear = now()->year;

        return view('tagihan-terjadwal.createBulk', compact('kategoriBiayaStatus', 'jenisBiayaOptions', 'tahunAjars', 'currentYear'));
    }

    public function generateBulkTagihanTerjadwal(Request $request)
    {
        $this->authorize('tagihan-terjadwal.create');

        try {
            $request->validate([
                'kategori_biaya_status' => 'nullable|string',
                'jenis_biaya_id' => 'nullable|exists:kategori_biayas,id_kategori_biaya',
                'tahun' => 'required|integer|min:2000|max:' . (now()->year + 2),
                'tahun_ajar_id' => 'nullable|exists:tahun_ajars,id_tahun_ajar',
            ]);

            // Validasi: minimal salah satu filter harus dipilih
            if (!$request->kategori_biaya_status && !$request->jenis_biaya_id) {
                return back()->withInput()->with('error', 'Pilih minimal salah satu: Kategori Biaya atau Jenis Biaya tertentu.');
            }

            $userId = Auth::id();
            $cacheKey = "bulk_tagihan_{$userId}";

            // Initialize progress tracking
            Cache::put($cacheKey, [
                'current' => 0,
                'total' => 0,
                'status' => 'initializing',
                'errors' => []
            ], 3600);

            $selectedKategoriStatus = $request->kategori_biaya_status;
            $selectedJenisBiayaId = $request->jenis_biaya_id;
            $selectedTahun = $request->tahun;
            $selectedTahunAjarId = $request->tahun_ajar_id;

            // Build query untuk BiayaSantri
            $biayaSantriQuery = BiayaSantri::with('daftarBiaya.kategoriBiaya');

            // if ($selectedKategoriStatus) {
            //     // Filter berdasarkan status kategori
            //     $biayaSantriQuery->whereHas('daftarBiaya.kategoriBiaya', function ($query) use ($selectedKategoriStatus) {
            //         $query->where('status', $selectedKategoriStatus);
            //     });
            // }

            if ($selectedJenisBiayaId) {
                // Filter berdasarkan jenis biaya tertentu
                $biayaSantriQuery->whereHas('daftarBiaya.kategoriBiaya', function ($query) use ($selectedJenisBiayaId) {
                    $query->where('id_kategori_biaya', $selectedJenisBiayaId);
                });
            }

            $user = auth()->user();

            // Role-based filtering for bulk generation
            if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
                // Ustadz can generate for: own data + santri they teach
                $allowedSantriIds = [$user->santri->id_santri]; // Own data
                $taughtSantriIds = $this->getSantriIdsUstadzTeaches($user->santri);
                $allowedSantriIds = array_merge($allowedSantriIds, $taughtSantriIds);
                $biayaSantriQuery->whereIn('santri_id', array_unique($allowedSantriIds));
            }

            // Get total count for progress tracking
            $totalBiayaSantris = $biayaSantriQuery->count();

            if ($totalBiayaSantris === 0) {
                return back()->withInput()->with('error', 'Tidak ada alokasi biaya santri dengan kategori tersebut untuk di-generate.');
            }

            // Update cache with total
            Cache::put($cacheKey, [
                'current' => 0,
                'total' => $totalBiayaSantris,
                'status' => 'processing',
                'errors' => []
            ], 3600);

            DB::beginTransaction();

            $processedCount = 0;
            $errorCount = 0;
            $errors = [];

            // Process in chunks to avoid memory issues
            $biayaSantriQuery->chunk(100, function ($biayaSantris) use ($selectedTahun, $selectedTahunAjarId, $cacheKey, &$processedCount, &$errorCount, &$errors) {
                foreach ($biayaSantris as $biayaSantri) {
                    try {
                        // Validasi relasi
                        if (!$biayaSantri->daftarBiaya || !$biayaSantri->daftarBiaya->kategoriBiaya) {
                            throw new Exception("BiayaSantri ID {$biayaSantri->id_biaya_santri} has broken relations");
                        }

                        $nominalTagihan = $biayaSantri->daftarBiaya->nominal * $biayaSantri->jumlah;

                        // Check for duplicates
                        $existingTagihanQuery = TagihanTerjadwal::where('biaya_santri_id', $biayaSantri->id_biaya_santri)
                            ->where('tahun', $selectedTahun);

                        if ($selectedTahunAjarId) {
                            $existingTagihanQuery->where('tahun_ajar_id', $selectedTahunAjarId);
                        } else {
                            $existingTagihanQuery->whereNull('tahun_ajar_id');
                        }

                        if (!$existingTagihanQuery->exists()) {
                            TagihanTerjadwal::create([
                                'santri_id' => $biayaSantri->santri_id,
                                'daftar_biaya_id' => $biayaSantri->daftarBiaya->id_daftar_biaya,
                                'biaya_santri_id' => $biayaSantri->id_biaya_santri,
                                'nominal' => $nominalTagihan,
                                'status' => 'belum_lunas',
                                'tahun' => $selectedTahun,
                                'tahun_ajar_id' => $selectedTahunAjarId,
                                'rincian' => [
                                    [
                                        'keterangan' => 'Tagihan otomatis: ' . $biayaSantri->daftarBiaya->kategoriBiaya->nama_kategori,
                                        'nominal' => $nominalTagihan
                                    ],
                                ],
                            ]);
                        }

                        $processedCount++;

                    } catch (Exception $e) {
                        $errorCount++;
                        $errors[] = "Error processing BiayaSantri ID {$biayaSantri->id_biaya_santri}: " . $e->getMessage();
                        Log::error("Bulk generate error: " . $e->getMessage());

                        // If too many errors, stop and rollback
                        if ($errorCount > 10) {
                            throw new Exception("Too many errors encountered. Process stopped.");
                        }
                    }

                    // Update progress
                    Cache::put($cacheKey, [
                        'current' => $processedCount + $errorCount,
                        'total' => Cache::get($cacheKey)['total'],
                        'status' => 'processing',
                        'errors' => $errors
                    ], 3600);
                }
            });

            if ($errorCount > 10) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Proses dihentikan karena terlalu banyak error.');
            }

            DB::commit();

            // Update final status
            Cache::put($cacheKey, [
                'current' => $processedCount + $errorCount,
                'total' => Cache::get($cacheKey)['total'],
                'status' => 'completed',
                'errors' => $errors,
                'processed' => $processedCount,
                'failed' => $errorCount
            ], 600); // Reduce cache time to 10 minutes after completion

            return redirect()->route('tagihan_terjadwal.index')->with('success', "Tagihan massal berhasil dibuat. {$processedCount} berhasil, {$errorCount} gagal.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->errors());
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Bulk generate error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getBulkProgress()
    {
        $this->authorize('tagihan-terjadwal.view');

        $userId = Auth::id();
        $cacheKey = "bulk_tagihan_{$userId}";
        $progress = Cache::get($cacheKey, ['status' => 'not_found']);

        return response()->json($progress);
    }

    public function edit($id_tagihan_terjadwal)
    {
        $this->authorize('update', $id_tagihan_terjadwal);

        try {
            $tagihanTerjadwal = TagihanTerjadwal::with(['santri', 'daftarBiaya.kategoriBiaya', 'biayaSantri', 'tahunAjar'])
                ->findOrFail($id_tagihan_terjadwal);

            // Check access to santri
            $this->checkSantriAccess($tagihanTerjadwal->santri_id);

            $user = auth()->user();
            $query = Santri::where('status', 'aktif');

            // Role-based filtering for santri selection
            if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
                // Ustadz can edit for: own data + santri they teach
                $allowedSantriIds = [$user->santri->id_santri]; // Own data
                $taughtSantriIds = $this->getSantriIdsUstadzTeaches($user->santri);
                $allowedSantriIds = array_merge($allowedSantriIds, $taughtSantriIds);
                $query->whereIn('id_santri', array_unique($allowedSantriIds));
            }

            $santris = $query->get();
            $tahunAjars = TahunAjar::all();

            // Get BiayaSantri untuk santri yang sedang ditagih dengan filter status 'tahunan' dan 'insidental'
            $biayaSantrisUntukSantri = BiayaSantri::where('santri_id', $tagihanTerjadwal->santri_id)
                ->with('daftarBiaya.kategoriBiaya')
                ->whereHas('daftarBiaya.kategoriBiaya', function ($query) {
                    $query->whereIn('status', ['tahunan', 'insidental']);
                })
                ->get()
                ->map(function ($biayaSantri) {
                    if (!$biayaSantri->daftarBiaya || !$biayaSantri->daftarBiaya->kategoriBiaya) {
                        return null;
                    }

                    $nominalGabungan = $biayaSantri->daftarBiaya->nominal * $biayaSantri->jumlah;
                    $statusKategori = $biayaSantri->daftarBiaya->kategoriBiaya->status;

                    return [
                        'id' => $biayaSantri->id_biaya_santri,
                        'nama_biaya_paket' => $biayaSantri->daftarBiaya->kategoriBiaya->nama_kategori .
                            ' [' . ucfirst($statusKategori) . ']' .
                            ' (Rp ' . number_format($nominalGabungan, 0, ',', '.') .
                            ($biayaSantri->jumlah > 1 ? ' x ' . $biayaSantri->jumlah : '') . ')',
                        'daftar_biaya_id' => $biayaSantri->daftarBiaya->id_daftar_biaya,
                        'nominal_tagihan_default' => $nominalGabungan,
                        'status_kategori' => $statusKategori,
                    ];
                })
                ->filter();

            return view('tagihan-terjadwal.edit', compact('tagihanTerjadwal', 'santris', 'tahunAjars', 'biayaSantrisUntukSantri'));

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        } catch (Exception $e) {
            Log::error('Error in edit method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id_tagihan_terjadwal)
    {
        $this->authorize('update', $id_tagihan_terjadwal);

        try {
            DB::beginTransaction();

            $tagihanTerjadwal = TagihanTerjadwal::findOrFail($id_tagihan_terjadwal);

            // Check access to santri
            $this->checkSantriAccess($tagihanTerjadwal->santri_id);

            $validatedData = $request->validate([
                'santri_id' => 'required|exists:santris,id_santri',
                'biaya_santri_id' => 'required|exists:biaya_santris,id_biaya_santri',
                'daftar_biaya_id' => 'required|exists:daftar_biayas,id_daftar_biaya',
                'nominal' => 'required|numeric|min:0',
                'status' => ['required', Rule::in(['belum_lunas', 'dibayar_sebagian', 'lunas'])],
                'tahun' => 'required|integer|min:2000|max:' . (now()->year + 2),
                'tahun_ajar_id' => 'nullable|exists:tahun_ajars,id_tahun_ajar',
            ]);

            // Check access to new santri if changed
            $this->checkSantriAccess($validatedData['santri_id']);

            // Check for duplicates (excluding current record)
            $existsQuery = TagihanTerjadwal::where('biaya_santri_id', $validatedData['biaya_santri_id'])
                ->where('tahun', $validatedData['tahun'])
                ->where('id_tagihan_terjadwal', '!=', $id_tagihan_terjadwal);

            if ($validatedData['tahun_ajar_id']) {
                $existsQuery->where('tahun_ajar_id', $validatedData['tahun_ajar_id']);
            } else {
                $existsQuery->whereNull('tahun_ajar_id');
            }

            if ($existsQuery->exists()) {
                return back()->withInput()->withErrors([
                    'biaya_santri_id' => 'Tagihan untuk alokasi biaya, tahun, dan tahun ajar yang dipilih sudah ada.'
                ]);
            }

            $updateData = [
                'santri_id' => $validatedData['santri_id'],
                'daftar_biaya_id' => $validatedData['daftar_biaya_id'],
                'biaya_santri_id' => $validatedData['biaya_santri_id'],
                'nominal' => $validatedData['nominal'],
                'status' => $validatedData['status'],
                'tahun' => $validatedData['tahun'],
                'tahun_ajar_id' => $validatedData['tahun_ajar_id'],
            ];

            $tagihanTerjadwal->update($updateData);

            DB::commit();
            return redirect()->route('tagihan_terjadwal.index')->with('success', 'Tagihan berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->errors());
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating tagihan: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui tagihan: ' . $e->getMessage());
        }
    }

    public function destroy($id_tagihan_terjadwal)
    {
        $this->authorize('delete', $id_tagihan_terjadwal);

        try {
            DB::beginTransaction();

            $tagihanTerjadwal = TagihanTerjadwal::findOrFail($id_tagihan_terjadwal);

            // Check access to santri
            $this->checkSantriAccess($tagihanTerjadwal->santri_id);

            $tagihanTerjadwal->delete(); // Soft delete

            DB::commit();
            return redirect()->route('tagihan_terjadwal.index')->with('success', 'Tagihan berhasil dihapus.');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting tagihan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus tagihan: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $this->authorize('tagihan-terjadwal.export');

        try {
            // Apply same filters as index
            $filters = $request->only(['tahun', 'status', 'jenis_biaya', 'search']);

            $filename = 'tagihan_terjadwal';
            if ($request->filled('tahun')) {
                $filename .= '_' . $request->tahun;
            }
            if ($request->filled('status')) {
                $filename .= '_' . $request->status;
            }
            $filename .= '.xlsx';

            return Excel::download(new TagihanTerjadwalExport($filters), $filename);

        } catch (Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
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
