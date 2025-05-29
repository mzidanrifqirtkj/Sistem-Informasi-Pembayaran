<?php

// app/Http/Controllers/BiayaSantriController.php
namespace App\Http\Controllers;

use App\Models\BiayaSantri;
use App\Models\DaftarBiaya;
use App\Models\Santri;
use Illuminate\Http\Request;

class BiayaSantriController extends Controller
{
    public function index()
    {
        $santris = Santri::with(['biayaSantris.daftarBiaya.kategoriBiaya'])
            ->whereHas('biayaSantris')
            ->get()
            ->map(function ($santri) {
                $santri->total_biaya = $santri->biayaSantris->sum(function ($biaya) {
                    return $biaya->daftarBiaya->nominal * $biaya->jumlah;
                });
                return $santri;
            });

        return view('biaya-santris.index', compact('santris'));
    }

    public function create()
    {
        $daftarBiayas = DaftarBiaya::with('kategoriBiaya')->get();
        $santris = Santri::all();
        return view('biaya-santris.create', compact('daftarBiayas', 'santris'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri',
            'biaya' => 'required|array',
            'biaya.*.id' => 'required|exists:daftar_biayas,id_daftar_biaya',
            'biaya.*.jumlah' => 'required|numeric|min:1',
        ]);

        foreach ($request->biaya as $item) {
            BiayaSantri::create([
                'santri_id' => $request->santri_id,
                'daftar_biaya_id' => $item['id'],
                'jumlah' => $item['jumlah'],
            ]);
        }

        return redirect()->route('biaya-santris.index')->with('success', 'Biaya santri berhasil ditambahkan');
    }

    public function searchSantri(Request $request)
    {
        $search = $request->q;
        $santris = Santri::where('nama_santri', 'like', "%$search%")->get();

        return response()->json($santris);
    }

    public function searchBiaya(Request $request)
    {
        $search = $request->q;
        $biayas = DaftarBiaya::with('kategoriBiaya')
            ->whereHas('kategoriBiaya', function ($query) use ($search) {
                $query->where('nama_kategori', 'like', "%$search%");
            })
            ->orWhere('nominal', 'like', "%$search%")
            ->get();

        return response()->json($biayas);
    }

    public function show($id)
    {
        $santri = Santri::with(['biayaSantris.daftarBiaya.kategoriBiaya'])
            ->findOrFail($id);

        $totalBiaya = $santri->biayaSantris->sum(function ($biaya) {
            return $biaya->daftarBiaya->nominal * $biaya->jumlah;
        });

        return view('biaya-santris.show', compact('santri', 'totalBiaya'));
    }

    public function edit($id)
    {
        $santri = Santri::with(['biayaSantris.daftarBiaya.kategoriBiaya'])
            ->findOrFail($id);

        $daftarBiayas = DaftarBiaya::with('kategoriBiaya')->get();
        $santris = Santri::all();

        return view('biaya-santris.edit', compact('santri', 'daftarBiayas', 'santris'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri',
            'biaya' => 'required|array',
            'biaya.*.id' => 'required|exists:daftar_biayas,id_daftar_biaya',
            'biaya.*.jumlah' => 'required|numeric|min:1',
        ]);

        // Delete all existing biaya for this santri
        BiayaSantri::where('santri_id', $id)->delete();

        // Create new biaya records
        foreach ($request->biaya as $item) {
            BiayaSantri::create([
                'santri_id' => $request->santri_id,
                'daftar_biaya_id' => $item['id'],
                'jumlah' => $item['jumlah'],
            ]);
        }

        return redirect()->route('biaya-santris.show', $request->santri_id)
            ->with('success', 'Paket biaya santri berhasil diperbarui');
    }

    public function destroy($id)
    {
        // Delete all biaya records for this santri
        BiayaSantri::where('santri_id', $id)->delete();

        return redirect()->route('biaya-santris.index')
            ->with('success', 'Paket biaya santri berhasil dihapus');
    }
}
