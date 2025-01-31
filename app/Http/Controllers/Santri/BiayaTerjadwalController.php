<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\BiayaTerjadwal;
use App\Models\KategoriSantri;
use Illuminate\Http\Request;

class BiayaTerjadwalController extends Controller
{
    public function index()
    {
        $biayaTerjadwals = BiayaTerjadwal::all();
        $kategoriSantri = KategoriSantri::all();
        return view('santris.biaya-terjadwal.index', compact('biayaTerjadwals', 'kategoriSantri'));
    }
}
