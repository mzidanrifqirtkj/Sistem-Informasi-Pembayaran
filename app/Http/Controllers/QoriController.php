<?php

namespace App\Http\Controllers;

use App\Models\QoriKelas;
use Illuminate\Http\Request;

class QoriController extends Controller
{
    public function index()
    {
        $qoris = QoriKelas::all();
        return view('qori.index', compact('qoris'));
    }

    public function create()
    {
        return view('qori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_qori' => 'required|string|max:255',
            // Tambahkan validasi lain jika diperlukan
        ]);

        QoriKelas::create([
            'nama_qori' => $request->nama_qori,
            // Tambahkan field lain jika ada
        ]);

        return redirect()->route('qori.index')->with('success', 'Qori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $qori = QoriKelas::findOrFail($id);
        return view('qori.edit', compact('qori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_qori' => 'required|string|max:255',
        ]);

        $qori = QoriKelas::findOrFail($id);
        $qori->update([
            'nama_qori' => $request->nama_qori,
        ]);

        return redirect()->route('qori.index')->with('success', 'Qori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $qori = QoriKelas::findOrFail($id);
        $qori->delete();

        return redirect()->route('qori.index')->with('success', 'Qori berhasil dihapus.');
    }
}
