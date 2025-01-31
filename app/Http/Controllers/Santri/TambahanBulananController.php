<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class TambahanBulananController extends Controller
{
    public function itemSantri()
    {
        $santris = Santri::with(['tambahanBulanans', 'kategoriSantri'])->get();
        return view('santris.tambahan-bulanan.item-santri', compact('santris'));
    }
}
