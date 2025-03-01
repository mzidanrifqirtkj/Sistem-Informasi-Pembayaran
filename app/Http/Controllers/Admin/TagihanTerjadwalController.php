<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BiayaTerjadwal;
use App\Models\Santri;
use App\Models\TagihanTerjadwal;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TagihanTerjadwalController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Ambil user yang sedang login
        $tagihanTerjadwals = collect(); // Inisialisasi koleksi kosong

        if ($user->hasRole('admin')) {
            // Jika user adalah admin, ambil semua data tagihan terjadwal dengan pagination
            $tagihanTerjadwals = TagihanTerjadwal::with(['santri', 'biayaTerjadwal'])->paginate(10);
        } elseif ($user->hasRole('santri')) {
            // Jika user adalah santri, ambil data tagihan terjadwal yang sesuai dengan user yang login
            $santri = $user->santri;
            if ($santri) { // Pastikan relasi santri ada
                $tagihanTerjadwals = TagihanTerjadwal::with(['santri', 'biayaTerjadwal'])
                    ->where('santri_id', $santri->id_santri)
                    ->paginate(10); // Gunakan paginate() untuk konsistensi
            }
        }

        return view('tagihan-terjadwal.index', compact('tagihanTerjadwals'));
    }
    // public function generateBulkTagihan()
    // {
    //     try {
    //         DB::beginTransaction();

    //         // Ambil semua santri
    //         $santris = Santri::all();

    //         // Ambil biaya terjadwal (asumsi ada data biaya yang ingin digunakan)
    //         $biayaTerjadwal = BiayaTerjadwal::all();

    //         if ($biayaTerjadwal->isEmpty()) {
    //             return redirect()->back()->with('error', 'Tidak ada data biaya terjadwal.');
    //         }

    //         foreach ($santris as $santri) {
    //             foreach ($biayaTerjadwal as $biaya) {
    //                 // Cek apakah tagihan sudah ada untuk kombinasi santri dan biaya
    //                 $existingTagihan = TagihanTerjadwal::where('santri_id', $santri->id_santri)
    //                     ->where('biaya_terjadwal_id', $biaya->id_biaya_terjadwal)
    //                     ->first();

    //                 if (!$existingTagihan) {
    //                     // Buat tagihan baru
    //                     TagihanTerjadwal::create([
    //                         'santri_id' => $santri->id_santri,
    //                         'biaya_terjadwal_id' => $biaya->id_biaya_terjadwal,
    //                         'nominal' => $biaya->nominal, // Asumsi nominal ada di tabel biaya_terjadwal
    //                         'status' => 'Belum Dibayar',
    //                         'rincian' => [
    //                             ['keterangan' => 'Tagihan otomatis', 'nominal' => $biaya->nominal],
    //                         ],
    //                     ]);
    //                 }
    //             }
    //         }

    //         DB::commit();
    //         return redirect()->back()->with('success', 'Tagihan berhasil dibuat untuk semua santri.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }
    public function createBulkTagihanTerjadwal()
    {
        $biayaTerjadwals = BiayaTerjadwal::all();
        return view('tagihan-terjadwal.createBulk', compact('biayaTerjadwals'));
    }

    public function generateBulkTagihanTerjadwal(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'biaya_terjadwal_id' => 'required|exists:biaya_terjadwals,id_biaya_terjadwal',
            ]);

            // Ambil biaya terjadwal
            $biayaTerjadwal = BiayaTerjadwal::where('id_biaya_terjadwal', $request->biaya_terjadwal_id)->first();

            if (is_null($biayaTerjadwal)) {
                return redirect()->back()->with('error', 'Tidak ada data biaya terjadwal.');
            }

            // Proses semua santri dengan chunk
            Santri::chunk(100, function ($santris) use ($biayaTerjadwal) {
                foreach ($santris as $santri) {
                    // Cek apakah tagihan sudah ada
                    $existingTagihan = TagihanTerjadwal::where('santri_id', $santri->id_santri)
                        ->where('biaya_terjadwal_id', $biayaTerjadwal->id_biaya_terjadwal)
                        ->first();

                    if (!$existingTagihan) {
                        $data = [
                            'santri_id' => $santri->id_santri,
                            'biaya_terjadwal_id' => $biayaTerjadwal->id_biaya_terjadwal,
                            'nominal' => $biayaTerjadwal->nominal, // Ambil nominal dari biaya terjadwal
                            'status' => 'belum_lunas',
                            'rincian' => [
                                ['keterangan' => 'Tagihan otomatis', 'nominal' => $biayaTerjadwal->nominal],
                            ],
                        ];

                        if ($biayaTerjadwal->periode == "tahunan") {
                            $data['tahun'] = now()->year;
                        } else {
                            $data['tahun'] = Carbon::parse($santri->tanggal_masuk)->year;
                        }
                        // Buat tagihan baru
                        TagihanTerjadwal::create($data);
                    }
                }
            });

            DB::commit();
            return redirect()->route('admin.tagihan_terjadwal.index')->with('success', 'Tagihan berhasil dibuat untuk semua santri menggunakan chunk.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $santris = Santri::all();
        $biayaTerjadwals = BiayaTerjadwal::all();
        $now = (int) date('Y');
        return view('tagihan-terjadwal.create', compact('santris', 'biayaTerjadwals', 'now'));
    }
    public function store(Request $request)
    {
        // Validasi Input
        try {
            $validatedData = $request->validate([
                'santri_id' => 'required|exists:santris,id_santri',
                'tahun' => 'nullable|integer|min:2000|max:' . (now()->year + 1),
                'biaya_terjadwal_id' => 'required|exists:biaya_terjadwals,id_biaya_terjadwal',
            ]);

            $santriId = $validatedData['santri_id'];
            $biayaId = $validatedData['biaya_terjadwal_id'];

            // Ambil informasi santri dan biaya
            $santri = Santri::findOrFail($santriId);
            $tahunMasukSantri = Carbon::parse($santri->tanggal_masuk)->year;
            $biaya = BiayaTerjadwal::findOrFail($biayaId);
            $tipe = $biaya->periode; // 'tahunan', 'semesteran', 'sekali'

            // Cek duplikasi berdasarkan tipe
            $exists = false;

            if ($tipe === 'tahunan') {
                $tahun = $validatedData['tahun'] ?? now()->year;
                // Hanya boleh ada satu tagihan per tahun
                $exists = TagihanTerjadwal::where('santri_id', $santriId)
                    ->where('biaya_terjadwal_id', $biayaId)
                    ->where('tahun', $tahun)
                    ->exists();
            } elseif ($tipe === 'sekali') {
                // Tidak boleh ada tagihan sebelumnya
                $exists = TagihanTerjadwal::where('santri_id', $santriId)
                    ->where('biaya_terjadwal_id', $biayaId)
                    ->exists();
            }

            // Jika sudah ada, kirim error
            if ($exists) {
                return back()->withErrors(['biaya_terjadwal_id' => 'Tagihan untuk kombinasi ini sudah ada.']);
            }
            $rincian = [
                "keterangan" => "Dibuat dengan sistem",
            ];
            $data = [
                'santri_id' => $santriId,
                'biaya_terjadwal_id' => $biayaId,
                'tahun' => $tahun,
                'nominal' => $biaya->nominal,
                'rincian' => $rincian,
                'status' => 'belum_lunas', // Default status
            ];
            if ($tipe === 'sekali') {
                $data['tahun'] = $tahunMasukSantri;
            }
            // Simpan tagihan baru
            TagihanTerjadwal::create($data);

            return redirect()->route('admin.tagihan_terjadwal.index')->with('success', 'Tagihan berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withErrors(['biaya_terjadwal_id' => 'Gagal membuat tagihan ' . $e->getMessage()]);
        }
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'santri_id' => 'required|exists:santris,id_santri',
    //         'biaya_terjadwal_id' => 'required|exists:biaya_terjadwals,id_biaya_terjadwal',
    //     ]);

    //     try {
    //         $dataTagihan = $request->only([
    //             'santri_id',
    //             'biaya_terjadwal_id',
    //         ]);
    //         $santri = Santri::findOrFail($request->santri_id);
    //         $biayaTerjadwal = BiayaTerjadwal::findOrFail($request->biaya_terjadwal_id);
    //         $isExistTagihan = TagihanTerjadwal::where('santri_id', $request->santri_id)
    //             ->where('tagihan_terjadwal_id', $request->biaya_terjadwal_id)
    //             ->exists();
    //         if ($isExistTagihan) {
    //             return redirect()->back()
    //                 ->with('error', 'Tagihan' . $biayaTerjadwal->nama_biaya . 'sudah dibuat');
    //         }

    //         $dataTagihan['nominal'] = $biayaTerjadwal->nominal;
    //         $rincian = [
    //             'biaya_terjadwal' => $biayaTerjadwal->nama_biaya,
    //             'nominal' => $biayaTerjadwal->nominal,
    //             // 'tabungan' => $santri->tabungan,
    //             // 'total bayar' => $dataTagihan['nominal'],
    //         ];

    //         $dataTagihan['status'] = 'belum_lunas';
    //         $dataTagihan['tahun'] = now()->year;
    //         $dataTagihan['rincian'] = $rincian;
    //         TagihanTerjadwal::create($dataTagihan);
    //         return redirect()->route('admin.tagihan_terjadwal.index')->with('success', 'Tagihan berhasil ditambahkan.');
    //     } catch (ModelNotFoundException $e) {
    //         throw new Exception('Data yang diminta tidak ditemukan. Harap periksa kembali.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', $e->getMessage());
    //     }
    // }

    public function edit(Request $tagihanTerjadwal)
    {
        $biayaTerjadwals = BiayaTerjadwal::all()->with(['tagihanTerjadwal']);

        try {
            $tagihanTerjadwal = TagihanTerjadwal::findOrFail($tagihanTerjadwal);
            return view('admin.tagihan-terjadwal.edit', compact('tagihanTerjadwal', 'biayaTerjadwals'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }
    }
    public function update(Request $request, $tagihan)
    {
        $tagihanTerjadwal = TagihanTerjadwal::findOrFail($tagihan);
        // Validasi input
        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri',
            'biaya_terjadwal_id' => 'required|exists:biaya_terjadwals,id_biaya_terjadwal',
            'nominal' => 'required|numeric',
        ]);

        try {
            // Update sesuai jenis tagihan
            $updateData = $request->only([
                'santri_id',
                'biaya_terjadwal_id',
                'nominal',
            ]);

            $tagihan->update($updateData);

            return redirect()->route('admin.tagihan_terjadwal.index')->with('success', 'Tagihan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui tagihan.');
        }
    }
}
