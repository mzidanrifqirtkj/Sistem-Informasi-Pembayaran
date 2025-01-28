<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        return view('mata-pelajaran.index', compact('mapels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mata-pelajaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_mapel' => 'required|string|max:255',
            ]);

            MataPelajaran::create($request->all());
            return redirect()->route('admin.mapel.index')->with('alert', 'Mata Pelajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.mapel.index')->with('error', 'Terjadi kesalahan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MataPelajaran $mataPelajaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MataPelajaran $mataPelajaran)
    {
        return view('mata-pelajaran.edit', compact('mataPelajaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        try {
            $request->validate([
                'nama_mapel' => 'required|string|max:255',
            ]);

            $mataPelajaran->update($request->all());
            return redirect()->route('admin.mapel.index')->with('alert', 'Mata Pelajaran berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('admin.mapel.index')->with('error', 'Terjadi kesalahan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $mataPelajaran = MataPelajaran::findOrFail($id);
            $mataPelajaran->delete();
            return redirect()->route('admin.mapel.index')->with('alert', 'Mata Pelajaran berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.mapel.index')->with('error', 'Terjadi kesalahan');
        }
    }
}
