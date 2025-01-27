<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pembayaran;
use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\TagihanBulanan;
use App\Models\TagihanTerjadwal;
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
    //     return view('admin.pembayaran.index', compact('dataPembayarans'));
    // }

    public function index()
    {
        // $dataPembayarans = Pembayaran::with(['tagihanBulanan', 'tagihanTahunan']);
        $santris = Santri::with('kategori_santri')->get();
        return view('pembayaran.index', compact('santris'));
    }
    public function riwayat()
    {
        $now = now()->year;
        $santris = Santri::with([
            'tagihanBulanan' => function ($query) use ($now) {
                $query->where('tahun', $now);
            }
        ])->paginate(10);
        // dd($santris);
        $dataPembayarans = Pembayaran::with(['tagihanBulanan.santri', 'tagihanTerjadwal.santri'])->get();
        // dd($dataPembayarans);
        // dd($dataPembayarans->first()->tagihanBulanan);
        return view('pembayaran.riwayat', compact('santris', 'now', 'dataPembayarans'));
    }

    // Menampilkan tagihan berdasarkan santri yang dipilih
    public function show($santriId)
    {

        $santri = Santri::with('kategori_santri',  'tagihanBulanan', 'tagihanTerjadwal.biayaTerjadwal')->findOrFail($santriId);
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
}
