<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BiayaTerjadwal;
use App\Models\JenisPembayaran;
use App\Models\KategoriSantri;
use Illuminate\Http\Request;

class BiayaTerjadwalController extends Controller
{
    public function index()
    {
        $biayaTerjadwals = BiayaTerjadwal::all();
        $kategoriSantri = KategoriSantri::all();
        return view('biaya-terjadwal.index', compact('biayaTerjadwals', 'kategoriSantri'));
    }
    public function create()
    {
        return view('biaya-terjadwal.create');
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_biaya' => 'required|string|max:255',
                'periode' => 'required|string|max:255',
                'nominal' => 'required|numeric|min:0',
            ]);

            // dd($request->all());
            BiayaTerjadwal::create([
                'nama_biaya' => $request->nama_biaya,
                'periode' => $request->periode,
                'nominal' => $request->nominal,
            ]);

            return redirect()->route('biaya_terjadwal.index')->with('alert', 'Biaya terjadwal berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function edit($id)
    {
        try {
            $data = BiayaTerjadwal::findOrFail($id);
            return view('biaya-terjadwal.edit', compact('data'));
        }
        // catch error
        catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_biaya' => 'required|string|max:255',
                'periode' => 'required|in:tahunan,sekali',
                'nominal' => 'required|numeric|min:0',
            ]);
            $biaya_terjadwal = BiayaTerjadwal::findOrFail($id);
            $biaya_terjadwal->update([
                'nama_biaya' => $request->nama_biaya,
                'periode' => $request->periode,
                'nominal' => $request->nominal,
            ]);
            return redirect()->route('biaya_terjadwal.index')->with('alert', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
