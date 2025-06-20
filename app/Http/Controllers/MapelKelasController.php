<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MapelKelas;
use App\Models\MataPelajaran;
use App\Models\QoriKelas;
use App\Models\TahunAjar;
use Illuminate\Http\Request;

class MapelKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahun_ajar_aktif = TahunAjar::where('status', 'aktif')->pluck('id_tahun_ajar');
        $mapelKelas = MapelKelas::whereIn('tahun_ajar_id', $tahun_ajar_aktif)->with(['kelas', 'mataPelajaran', 'tahunAjar', 'qoriKelas'])->get();
        return view('mapel-kelas.index', compact('mapelKelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::all();
        $mapel = MataPelajaran::all();
        $tahunAjar = TahunAjar::all();
        $qoriKelas = QoriKelas::where('status', 'aktif')->get();
        return view('mapel-kelas.create', compact('kelas', 'mapel', 'tahunAjar', 'qoriKelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'tahun_ajar_id' => 'required|exists:tahun_ajars,id_tahun_ajar',
            'mapel_id' => 'required|exists:mata_pelajarans,id_mapel',
            'qori_id' => 'required|exists:qori_kelas,id_qori_kelas',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        try {
            MapelKelas::create([
                'kelas_id' => $request->kelas_id,
                'tahun_ajar_id' => $request->tahun_ajar_id,
                'qori_id' => $request->qori_id,
                'mapel_id' => $request->mapel_id,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai
            ]);

            return redirect()->route('mapel_kelas.index')
                ->with('success', 'Pelajaran berhasil ditambahkan ke kelas!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MapelKelas $mapelKelas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MapelKelas $mapelKelas)
    {
        $qoriKelas = QoriKelas::with('santri')
            ->where('status', 'aktif')
            ->has('santri') // Hanya yang punya relasi santri
            ->get();
        $tahunAjar = TahunAjar::all();
        $kelas = Kelas::all();
        $mapel = MataPelajaran::all();

        return view('mapel-kelas.edit', compact(
            'mapelKelas',
            'qoriKelas',
            'tahunAjar',
            'kelas',
            'mapel'
        ));
    }

    public function update(Request $request, MapelKelas $mapelKelas)
    {
        $request->validate([
            'qori_id' => 'required|exists:qori_kelas,id_qori_kelas',
            'tahun_ajar_id' => 'required|exists:tahun_ajars,id_tahun_ajar',
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'mapel_id' => 'required|exists:mata_pelajarans,id_mapel',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $data = $request->only([
            'qori_id',
            'tahun_ajar_id',
            'kelas_id',
            'mapel_id',
            'jam_mulai',
            'jam_selesai'
        ]);

        $updated = $mapelKelas->update($data);

        if (!$updated) {
            return back()->with('error', 'Gagal memperbarui data');
        }

        return redirect()->route('mapel_kelas.index')
            ->with('success', 'Data berhasil diperbarui!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MapelKelas $mapelKelas)
    {
        $mapelKelas->delete();
        return redirect()->route('mapel_kelas.index')->with('alert', 'Data berhasil dihapus.');
    }
}
