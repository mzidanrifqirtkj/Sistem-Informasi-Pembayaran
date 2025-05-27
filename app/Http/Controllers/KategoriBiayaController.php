<?php

namespace App\Http\Controllers;

use App\Models\KategoriBiaya;
use Illuminate\Http\Request;

class KategoriBiayaController extends Controller
{
    public function index()
    {
        $kategoriBiayas = KategoriBiaya::all();
        return view('kategori-biayas.index', compact('kategoriBiayas'));
    }

    public function create()
    {
        return view('kategori-biayas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'status' => 'required|in:tahunan,eksidental,tambahan,jalur',
        ]);

        KategoriBiaya::create($request->all());

        return redirect()->route('kategori-biayas.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = KategoriBiaya::findOrFail($id);
        return view('kategori-biayas.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'status' => 'required|in:tahunan,eksidental,tambahan,jalur',
        ]);

        $kategori = KategoriBiaya::findOrFail($id);
        $kategori->update($request->all());

        return redirect()->route('kategori-biayas.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = KategoriBiaya::findOrFail($id);
        $kategori->delete();

        return redirect()->route('kategori-biayas.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
