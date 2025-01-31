<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\TagihanTerjadwal;
use Illuminate\Http\Request;

class TagihanTerjadwalController extends Controller
{
    public function index()
    {
        $tagihanTerjadwals = TagihanTerjadwal::with(['santri', 'biayaTerjadwal'])->paginate(10);
        // dd($tagihanTerjadwals);
        return view('santris.tagihan-terjadwal.index', compact('tagihanTerjadwals'));
    }
}
