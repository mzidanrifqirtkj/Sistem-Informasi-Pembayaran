<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\TagihanBulanan;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanBulananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user(); // Ambil user yang sedang login
        $now = now()->year; // Ambil tahun saat ini

        if ($user->hasRole('admin')) {
            // Jika user adalah admin, ambil semua data santri beserta tagihan bulanan untuk tahun ini
            $santris = \App\Models\Santri::with([
                'tagihanBulanan' => function ($query) use ($now) {
                    $query->where('tahun', $now); // Filter tagihan berdasarkan tahun
                }
            ])->paginate(10); // Paginasi data santri

            $dataTagihans = TagihanBulanan::with(['santri'])
                ->where('tahun', $now) // Filter tagihan berdasarkan tahun
                ->get();
        } elseif ($user->hasRole('santri')) {
            // Jika user adalah santri, ambil data tagihan bulanan yang terkait dengan santri tersebut
            $santri = $user->santri; // Ambil data santri dari user yang login

            if ($santri) {
                // Ambil data tagihan bulanan untuk santri tersebut pada tahun ini
                $dataTagihans = TagihanBulanan::with(['santri'])
                    ->where('santri_id', $santri->id_santri) // Filter berdasarkan santri_id
                    ->where('tahun', $now) // Filter berdasarkan tahun
                    ->get();

                // Untuk konsistensi, kita juga bisa mengirim data santri ke view
                $santris = Santri::with([
                    'tagihanBulanan' => function ($query) use ($now) {
                        $query->where('tahun', $now); // Filter tagihan berdasarkan tahun
                    }
                ])->where('id_santri', $santri->santri_id) // Filter santri berdasarkan id
                    ->paginate(10);
            } else {
                // Jika relasi santri tidak ditemukan, kembalikan koleksi kosong
                $dataTagihans = collect();
                $santris = collect();
            }
        } else {
            // Jika role tidak dikenali, kembalikan koleksi kosong
            $dataTagihans = collect();
            $santris = collect();
        }

        return view('tagihan-bulanan.index', compact('dataTagihans', 'santris', 'now'));
    }

    public function createBulkBulanan()
    {
        // $santris = Santri::with('');
        return view('tagihan-bulanan.createBulk');
    }

    public function generateBulkBulanan(Request $request)
    {
        $request->validate([
            'bulan' => 'required|in:Jan,Feb,Mar,Apr,May, Jun, Jul, Aug,Sep, Oct, Nov, Dec',
            'tahun' => 'required|numeric|digits:4'
        ]);
        try {
            $bulan = $request->bulan;
            $tahun = $request->tahun;

            DB::beginTransaction();
            // Ambil semua tagihan untuk bulan dan tahun yang dimaksud
            $existingTagihan = TagihanBulanan::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->pluck('santri_id')
                ->toArray();
            $idSantri = 1; // ID santri yang ingin diperiksa


            // dd($existingTagihan);

            Santri::with(['kategoriSantri', 'tambahanBulanans'])->chunk(100, function ($santris) use ($bulan, $tahun, $existingTagihan) {
                foreach ($santris as $santri) {

                    $idSantri = $santri->id_santri;
                    $existID = in_array($idSantri, $existingTagihan);

                    if (!$existID) {

                        $rincian = [
                            'syahriyah' => [
                                'nominal' => $santri->kategoriSantri->nominal_syahriyah ?? 0,
                            ],
                            'tambahan' => [],
                        ];

                        // Hitung nominal syahriyah
                        $nominal = $rincian['syahriyah']['nominal'];

                        // Tambahkan rincian tambahan pembayaran
                        foreach ($santri->tambahanBulanans as $tambahan) {
                            if (!isset($tambahan->pivot->jumlah) || !isset($tambahan->nominal)) {
                                continue;
                            }

                            $rincian['tambahan'][] = [
                                'nama_item' => $tambahan->nama_item,
                                'nominal' => $tambahan->nominal,
                                'jumlah' => $tambahan->pivot->jumlah,
                                'tahun' => now()->year,
                            ];

                            $nominal += ($tambahan->pivot->jumlah * $tambahan->nominal);
                        }

                        // Buat data tagihan baru
                        $data = [
                            'santri_id' => $santri->id_santri,
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                            'nominal' => $nominal,
                            'rincian' => $rincian,
                            'status' => 'belum_lunas',
                        ];

                        // Insert data ke dalam database
                        TagihanBulanan::create($data);
                    }
                }
            });


            DB::commit();
            return redirect()->route('admin.tagihan_bulanan.index')->with('alert', 'Tagihan bulanan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat tagihan bulanan: ' . $e->getMessage());
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $now = (int) date('Y');
        $santris = Santri::with(['kategoriSantri', 'tambahanBulanans'])->get();
        return view('tagihan-bulanan.create', compact('santris', 'now'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri',
            'bulan' => 'required|in:Jan,Feb,Mar,Apr,May, Jun, Jul, Aug,Sep, Oct, Nov, Dec',
            'tahun' => 'required|numeric|digits:4'
        ]);
        try {
            $santri = Santri::where('id_santri', $request->santri_id)
                ->with(['kategoriSantri', 'tambahanBulanans'])
                ->first();

            $isExistTagihan = TagihanBulanan::where('santri_id', $request->santri_id)
                ->where('bulan', $request->bulan)
                ->where('tahun', $request->tahun)
                ->exists();

            if ($isExistTagihan) {
                return back()
                    ->withErrors(['bulan', 'Tagihan Syahriah sudah dibuat.']);
            }

            $rincian['syahriyah'] = [
                'kategori' => 'syahriyah ' . $santri->kategoriSantri->nama_kategori,
                'nominal' => $santri->kategoriSantri->nominal_syahriyah,
            ];
            // menambahkan nominal syahriyah dan tambahan pembayaran dan membuat rincian
            $nominal = $santri->kategoriSantri->nominal_syahriyah;
            foreach ($santri->tambahanBulanans as $tambahan) {
                if (!isset($tambahan->pivot->jumlah) || !isset($tambahan->nominal)) {
                    continue;
                }

                $rincian['tambahan'][] = [
                    'nama_item' => $tambahan->nama_item,
                    'nominal' => $tambahan->nominal,
                    'jumlah' => $tambahan->pivot->jumlah,
                ];
                $nominal += ($tambahan->pivot->jumlah * $tambahan->nominal);
            }


            $tagihanBulanan = new TagihanBulanan();
            $tagihanBulanan->santri_id = $request->santri_id;
            $tagihanBulanan->bulan = $request->bulan;
            $tagihanBulanan->tahun = $request->tahun;
            $tagihanBulanan->status = 'belum_lunas';
            $tagihanBulanan->rincian = $rincian;
            $tagihanBulanan->nominal = $nominal;
            $tagihanBulanan->created_at = now();
            $tagihanBulanan->updated_at = now();
            $tagihanBulanan->save();

            return redirect()->route('admin.tagihan_bulanan.index')->with('alert', 'Tagihan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withErrors('santri_id', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TagihanBulanan $tagihanBulanan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TagihanBulanan $tagihanBulanan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TagihanBulanan $tagihanBulanan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TagihanBulanan $tagihanBulanan)
    {
        //
    }
}
