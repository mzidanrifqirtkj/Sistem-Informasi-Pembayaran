<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Santri;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function riwayat()
    {
        $santri = auth()->user()->santri;
        $now = now()->year;
        $santris = Santri::with([
            'tagihanBulanan' => function ($query) use ($now) {
                $query->where('tahun', $now);
            }
        ])->where('id_santri', $santri->id_santri)->paginate(10);

        $dataPembayarans = Pembayaran::with(['tagihanBulanan.santri', 'tagihanTerjadwal.santri'])
            ->whereHas('tagihanBulanan', function ($query) use ($santri) {
                $query->where('santri_id', $santri->id_santri);  // Pastikan 'santri_id' sesuai dengan relasi
            })
            ->get();
        return view('santris.pembayaran.riwayat', compact('santris', 'now', 'dataPembayarans'));
    }
}
