<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\TagihanBulanan;
use Illuminate\Http\Request;

class TagihanBulananController extends Controller
{
    public function index()
    {
        // $tagihanBulanans = TagihanBulanan::all();
        $now = now()->year;
        // $santris = Santri::with('tagihanBulanan')->paginate(5);
        $santris = \App\Models\Santri::with([
            'tagihanBulanan' => function ($query) use ($now) {
                $query->where('tahun', $now);
            }
        ])->paginate(10);
        // dd($santris);
        $dataTagihans = TagihanBulanan::with(['santri'])->get();

        return view('santris.tagihan-bulanan.index', compact('dataTagihans', 'santris', 'now'));
    }
}
