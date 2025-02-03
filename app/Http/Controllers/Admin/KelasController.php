<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\PenugasanUstadz;
use App\Models\TahunAjar;
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

    // public function createMapelKelas()
    // {
    //     $kelas = Kelas::all();
    //     $tahunAjar = TahunAjar::all();
    //     $mapel = MataPelajaran::all();

    //     return view('kelas.create-mapel', compact('kelas', 'tahunAjar', 'mapel'));
    // }

    // public function storeMapelKelas(Request $request)
    // {
    //     $request->validate([
    //         'id_kelas' => 'required',
    //         'id_tahun_ajar' => 'required',
    //         'id_mapel.*' => 'required', // Array pelajaran
    //     ]);

    //     foreach ($request->id_mapel as $mapel) {
    //         PenugasanUstadz::updateOrCreate(
    //             [
    //                 'kelas_id' => $request->id_kelas,
    //                 'tahun_ajar_id' => $request->id_tahun_ajar,
    //                 'mapel_id' => $mapel
    //             ],
    //             [] // Update tidak perlu data tambahan
    //         );
    //     }

    //     return back()->with('success', 'Pelajaran berhasil ditambahkan ke kelas!');
    // }
}
