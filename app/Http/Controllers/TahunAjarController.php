<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TahunAjar;
use Illuminate\Http\Request;

class TahunAjarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahun_ajar = TahunAjar::orderBy('tahun_ajar', 'asc')->get();
        return view('tahun-ajar.index', compact('tahun_ajar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tahun-ajar.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'tahun_ajar' => 'required|string|max:9',
            ]);

            TahunAjar::create($request->all());
            return redirect()->route('tahun_ajar.index')->with('alert', 'Tahun ajar berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('tahun_ajar.index')->with('error', 'Tahun ajar gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TahunAjar $tahunAjar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAjar $tahunAjar)
    {
        return view('tahun-ajar.edit', compact('tahunAjar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAjar $tahunAjar)
    {
        try {
            $request->validate([
                'tahun_ajar' => 'required|string|max:9',
            ]);

            $tahunAjar->update($request->all());
            return redirect()->route('tahun_ajar.index')->with('alert', 'Tahun ajar berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('tahun_ajar.index')->with('error', 'Tahun ajar gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAjar $tahunAjar)
    {
        try {
            $tahunAjar->delete();
            return redirect()->route('tahun_ajar.index')->with('alert', 'Tahun ajar berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('tahun_ajar.index')->with('error', 'Tahun ajar gagal dihapus');
        }
    }
}
