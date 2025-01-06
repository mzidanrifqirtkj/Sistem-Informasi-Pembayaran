<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function index()
    {
        // $santris = Santri::with(['user', 'kategori_santri'])->get();
        $santris = Santri::orderBy('user_id', 'asc')->with(['user', 'kategori_santri'])->get();
        return view('admin.data_santri', compact('santris'));

    }
}
