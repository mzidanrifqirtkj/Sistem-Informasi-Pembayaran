<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function showSantri(){
        $santris = Santri::with(['user', 'paket_pembayaran'])->get();

        return view('data_santri', compact('santris'));
    }
}
