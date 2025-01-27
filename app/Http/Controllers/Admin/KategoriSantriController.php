<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriSantri;
use Illuminate\Http\Request;

class KategoriSantriController extends Controller
{
    public function index()
    {
        $kategori_santris = KategoriSantri::orderBy('nama_kategori', 'asc')->get();
        return view('kategori-santri.index', compact('kategori_santris'));
    }
    public function create()
    {
        return view('kategori-santri.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'nominal_syahriyah' => 'required|numeric|regex:/^\d{1,8}(\.\d{1,2})?$/',
        ]);

        try {
            KategoriSantri::create([
                'nama_kategori' => $request->nama_kategori,
                'nominal_syahriyah' => $request->nominal_syahriyah,
            ]);

            return redirect()->route('admin.kategori.index')->with('success', 'Kategori santri berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan kategori santri: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $data = KategoriSantri::findOrFail($id);
        return view('kategori-santri.edit', compact('data'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'nominal_syahriyah' => 'required|numeric|regex:/^\d{1,8}(\.\d{1,2})?$/',
        ]);
        try {
            $kategori_santri = KategoriSantri::findOrFail($id);
            $kategori_santri->update([
                'nama_kategori' => $request->nama_kategori,
                'nominal_syahriyah' => $request->nominal_syahriyah,
            ]);

            return redirect()->route('admin.kategori.index')->with('success', 'Kategori santri berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui kategori santri: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $kategori_santri = KategoriSantri::findOrFail($id);
            $kategori_santri->delete();

            return redirect()->route('admin.biaya_terjadwal.index')->with('alert', 'Kategori santri berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus kategori santri: ' . $e->getMessage());
        }
    }
}
