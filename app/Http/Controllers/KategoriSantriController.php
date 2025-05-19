<?php

namespace App\Http\Controllers;

use App\Models\KategoriSantri;
use Illuminate\Http\Request;

class KategoriSantriController extends Controller
{
    public function index()
    {
        $kategoriSantri = KategoriSantri::orderBy('nama_kategori', 'asc')->get();
        return view('kategori-santri.index', compact('kategoriSantri'));
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

            return redirect()->route('kategori.index')->with('success', 'Kategori santri berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
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
            $kategori = KategoriSantri::findOrFail($id);
            $kategori->update([
                'nama_kategori' => $request->nama_kategori,
                'nominal_syahriyah' => $request->nominal_syahriyah,
            ]);

            return redirect()->route('kategori.index')->with('success', 'Kategori santri berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $kategori = KategoriSantri::findOrFail($id);
            $kategori->delete();

            return redirect()->route('kategori.index')->with('success', 'Kategori santri berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}
