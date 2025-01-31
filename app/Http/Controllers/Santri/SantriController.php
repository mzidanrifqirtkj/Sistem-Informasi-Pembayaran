<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function show(Santri $santri)
    {
        $santri->load('tambahanBulanans', 'user', 'kategoriSantri');
        return view('santris.santri.show', compact('santri'));
    }
}
