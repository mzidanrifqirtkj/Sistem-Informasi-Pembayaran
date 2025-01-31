<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Santri;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index()
    {
        // $dataPembayarans = Pembayaran::with(['tagihanBulanan', 'tagihanTahunan']);
        $santris = Santri::with('kategoriSantri')->get();
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

        $santri = Santri::with('kategoriSantri', 'tagihanBulanan', 'tagihanTerjadwal.biayaTerjadwal')->findOrFail($santriId);
        return view('pembayaran.show', compact('santri'));
    }
}
