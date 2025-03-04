<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\TagihanBulanan;
use Illuminate\Http\Request;

class TagihanBulananController extends Controller
{
    public function index()
    {
        $santri = auth()->user()->santri;
        $now = now()->year;
        $santris = \App\Models\Santri::with([
            'tagihanBulanan' => function ($query) use ($now) {
                $query->where('tahun', $now);
            }
        ])->where('id_santri', $santri->id_santri)->paginate(10);

        $dataTagihans = TagihanBulanan::with(['santri'])
            ->where('santri_id', $santri->id_santri)
            ->get();

        return view('santris.tagihan-bulanan.index', compact('dataTagihans', 'santris', 'now'));
    }
}
