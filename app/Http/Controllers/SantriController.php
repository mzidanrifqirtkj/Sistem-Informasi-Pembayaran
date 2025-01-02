<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function showSantri(){
        // $santris = Santri::with(['user', 'kategori_santri'])->get();
        $santris = Santri::orderBy('user_id', 'asc')->with(['user', 'kategori_santri'])->get();
        return view('data_santri', compact('santris'));
    }
}
