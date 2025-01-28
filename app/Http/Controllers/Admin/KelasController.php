<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::all();
        return view('kelas.index', compact('kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kelas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_kelas' => 'required',
            ]);

            Kelas::create($request->all());
            return redirect()->route('admin.kelas.index')->with('alert', 'Kelas berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.kelas.index')->with('error', 'Kelas gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        return view('kelas.edit', compact('kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        try {
            $request->validate([
                'nama_kelas' => 'required|string|max:255',
            ]);

            $kelas->update($request->all());
            // dd($kelas);
            return redirect()->route('admin.kelas.index')->with('alert', 'Kelas berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('admin.kelas.index')->with('error', 'Kelas gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);
            $kelas->delete();
            return redirect()->route('admin.kelas.index')->with('alert', 'Kelas berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.kelas.index')->with('error', 'Kelas gagal dihapus');
        }
    }
}
