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
        $biayaTerjadwals = BiayaTerjadwal::orderBy('periode', 'desc')->get();
        $kategoriSantri = KategoriSantri::all();
        return view('biaya-terjadwal.index', compact('biayaTerjadwals', 'kategoriSantri'));
    }

    public function create()
    {
        $periodeOptions = [
            'Tahunan' => 'Dana Tahunan',
            'Sekali' => 'Dana Eksidental'
        ];
        return view('biaya-terjadwal.create', compact('periodeOptions'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_biaya' => 'required|string|max:255',
                'periode' => 'required|in:Tahunan,Sekali',
                'nominal' => 'required|numeric|min:0',
            ]);

            BiayaTerjadwal::create([
                'nama_biaya' => $request->nama_biaya,
                'periode' => $request->periode,
                'nominal' => $request->nominal,
            ]);

            return redirect()->route('biaya_terjadwal.index')
                ->with('success', 'Biaya terjadwal berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan biaya: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $data = BiayaTerjadwal::findOrFail($id);
            $periodeOptions = [
                'Tahunan' => 'Dana Tahunan',
                'Sekali' => 'Dana Eksidental'
            ];
            return view('biaya-terjadwal.edit', compact('data', 'periodeOptions'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_biaya' => 'required|string|max:255',
                'periode' => 'required|in:Tahunan,Sekali',
                'nominal' => 'required|numeric|min:0',
            ]);

            $biaya_terjadwal = BiayaTerjadwal::findOrFail($id);
            $biaya_terjadwal->update([
                'nama_biaya' => $request->nama_biaya,
                'periode' => $request->periode,
                'nominal' => $request->nominal,
            ]);

            return redirect()->route('biaya_terjadwal.index')
                ->with('success', 'Data biaya berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $biaya = BiayaTerjadwal::findOrFail($id);
            $biaya->delete();

            return redirect()->route('biaya_terjadwal.index')
                ->with('success', 'Biaya berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus biaya: ' . $e->getMessage());
        }
    }
}
