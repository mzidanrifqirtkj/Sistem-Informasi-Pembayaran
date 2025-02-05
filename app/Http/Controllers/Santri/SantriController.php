<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function show()
    {
        $santri = auth()->user()->santri()->with('tambahanBulanans', 'kategoriSantri')->firstOrFail();
        return view('santris.santri.show', compact('santri'));
    }
}
