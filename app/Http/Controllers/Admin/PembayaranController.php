<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pembayaran;
use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\TagihanBulanan;
use App\Models\TagihanTerjadwal;
use Auth;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    // public function index()
    // {
    //     $dataPembayarans = Pembayaran::with([
    //         'tagihanTerjadwal',
    //         'santriTagihanTerjadwal',
    //         'santriTagihanBulanan',
    //         'tagihanBulanan'
    //     ])->get();
    //     return view('pembayaran.index', compact('dataPembayarans'));
    // }

    public function index()
    {
        $user = Auth::user(); // Ambil user yang sedang login

        if ($user->hasRole('admin')) {
            // Jika user adalah admin, ambil semua data santri
            $santris = Santri::with('kategoriSantri')->paginate(10);
        } elseif ($user->hasRole('santri')) {
            // Jika user adalah santri, ambil data santri yang sesuai dengan user yang login
            $santri = $user->santri; // Ambil data santri dari user yang login

            if ($santri) {
                // Ambil data santri yang login
                $santris = Santri::with('kategoriSantri')
                    ->where('id_santri', $santri->id_santri) // Filter berdasarkan id_santri
                    ->paginate(10);
            } else {
                // Jika relasi santri tidak ditemukan, kembalikan koleksi kosong
                $santris = collect();
            }
        } else {
            // Jika role tidak dikenali, kembalikan koleksi kosong
            $santris = collect();
        }

        return view('pembayaran.index', compact('santris'));
    }
    public function riwayat()
    {
        $user = Auth::user(); // Ambil user yang sedang login
        $now = now()->year; // Ambil tahun saat ini

        if ($user->hasRole('admin')) {
            // Jika user adalah admin, ambil semua data santri beserta tagihan bulanan untuk tahun ini
            $santris = Santri::with([
                'tagihanBulanan' => function ($query) use ($now) {
                    $query->where('tahun', $now); // Filter tagihan berdasarkan tahun
                }
            ])->paginate(10); // Paginasi data santri

            $dataPembayarans = Pembayaran::with(['tagihanBulanan.santri', 'tagihanTerjadwal.santri'])->get();
        } elseif ($user->hasRole('santri')) {
            // Jika user adalah santri, ambil data tagihan bulanan yang terkait dengan santri tersebut
            $santri = $user->santri; // Ambil data santri dari user yang login

            if ($santri) {
                // Ambil data tagihan bulanan untuk santri tersebut pada tahun ini
                $santris = Santri::with([
                    'tagihanBulanan' => function ($query) use ($now) {
                        $query->where('tahun', $now); // Filter tagihan berdasarkan tahun
                    }
                ])->where('id_santri', $santri->id_santri) // Filter santri berdasarkan id
                    ->paginate(10);

                // Ambil data pembayaran untuk santri tersebut
                $dataPembayarans = Pembayaran::with(['tagihanBulanan.santri', 'tagihanTerjadwal.santri'])
                    ->whereHas('tagihanBulanan', function ($query) use ($santri) {
                        $query->where('santri_id', $santri->id_santri);
                    })
                    ->orWhereHas('tagihanTerjadwal', function ($query) use ($santri) {
                        $query->where('santri_id', $santri->id_santri);
                    })
                    ->get();
            } else {
                // Jika relasi santri tidak ditemukan, kembalikan koleksi kosong
                $santris = collect();
                $dataPembayarans = collect();
            }
        } else {
            // Jika role tidak dikenali, kembalikan koleksi kosong
            $santris = collect();
            $dataPembayarans = collect();
        }

        return view('pembayaran.riwayat', compact('santris', 'now', 'dataPembayarans'));
    }

    // Menampilkan tagihan berdasarkan santri yang dipilih
    public function show($santriId)
    {

        $santri = Santri::with('kategoriSantri', 'tagihanBulanan', 'tagihanTerjadwal.biayaTerjadwal')->findOrFail($santriId);
        return view('pembayaran.show', compact('santri'));
    }

    //create pembayaran
    // public function create()
    // {
    //     try {
    //         $santri = Santri::all();
    //         $tagihanBulanan = TagihanBulanan::where('status', 'belum_lunas')->get();
    //         $tagihanTerjadwal = TagihanTerjadwal::where('status', 'belum_lunas')->get();

    //         return view('pembayaran.create', compact('santri', 'tagihanBulanan', 'tagihanTerjadwal'));
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', $e->getMessage());
    //     }
    // }

    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri',
            'jenis_tagihan' => 'required|in:bulanan,terjadwal',
            'tagihan_id' => 'required|numeric',
            // 'nominal' => 'required|numeric|min:1',
        ]);

        try {
            $santri = Santri::findOrFail($request->santri_id);

            if ($request->jenis_tagihan === 'bulanan') {
                $tagihan = TagihanBulanan::findOrFail($request->tagihan_id);
                // dd($tagihan->id_tagihan_bulanan);

                if ($tagihan->status === 'lunas') {
                    return back()->withErrors(['message' => 'Tagihan ini sudah lunas.']);
                }

                // Update tagihan bulanan
                // $tagihan->nominal = $request->nominal;
                // if ($tagihan->nominal >= $tagihan->nominal) {
                // }

                $data = [
                    // 'santri_id' => $santri->id_santri,
                    // 'jenis_tagihan' => $request->jenis_tagihan,
                    'tagihan_bulanan_id' => $tagihan->id_tagihan_bulanan,
                    'created_by_id' => $santri->user_id,
                    'nominal_pembayaran' => $tagihan->nominal,
                    'tanggal_pembayaran' => now(),
                ];
                // dd($data);
                $tagihan->status = 'lunas';
                $tagihan->save();
            } else {

                $tagihan = TagihanTerjadwal::findOrFail($request->tagihan_id);
                // dd($tagihan->id_tagihan_terjadwal);
                if ($tagihan->status === 'lunas') {
                    return back()->withErrors(['message' => 'Tagihan ini sudah lunas.']);
                }

                // Update tagihan terjadwal
                // $tagihan->nominal_terbayar += $request->nominal;
                // if ($tagihan->nominal_terbayar >= $tagihan->nominal) {
                // }
                // Pembayaran::create($tagihan);
                $data = [
                    'tagihan_terjadwal_id' => $tagihan->id_tagihan_terjadwal,
                    'created_by_id' => $santri->user_id,
                    'nominal_pembayaran' => $tagihan->nominal,
                    'tanggal_pembayaran' => now(),
                ];
                // dd($data);

                $tagihan->status = 'lunas';
                $tagihan->save();
            }

            Pembayaran::create($data);
            return redirect()->back()->with('alert', 'Pembayaran berhasil.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $pembayaran = Pembayaran::findOrFail($id);
            $pembayaran->delete();
            return redirect()->route('pembayaran.index')->with('alert', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
