<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\TagihanTerjadwal;
use Illuminate\Http\Request;

class TagihanTerjadwalController extends Controller
{
    public function index()
    {
        $santri = auth()->user()->santri; // Ambil data santri yang terkait dengan user
        if (!$santri) {
            abort(404, 'Santri tidak ditemukan untuk user ini');
        }

        $tagihanTerjadwals = TagihanTerjadwal::with(['santri', 'biayaTerjadwal'])
            ->where('santri_id', $santri->id_santri) // Filter berdasarkan santri yang login
            ->paginate(10);
        return view('santris.tagihan-terjadwal.index', compact('tagihanTerjadwals'));
    }
}
