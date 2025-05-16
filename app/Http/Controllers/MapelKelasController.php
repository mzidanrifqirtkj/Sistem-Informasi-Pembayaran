<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MapelKelas;
use App\Models\MataPelajaran;
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
        $mapelKelas = MapelKelas::whereIn('tahun_ajar_id', $tahun_ajar_aktif)->with(['kelas', 'mataPelajaran', 'tahunAjar', 'qoriKelass'])->get();
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
        return view('mapel-kelas.create', compact('kelas', 'mapel', 'tahunAjar'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());


        try {
            $request->validate([
                'id_kelas' => 'required|exists:kelas,id_kelas',
                'id_tahun_ajar' => 'required|exists:tahun_ajars,id_tahun_ajar',
                'id_mapel.*' => 'required|exists:mata_pelajarans,id_mapel',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            ]);

            foreach ($request->id_mapel as $mapel) {
                MapelKelas::updateOrCreate(
                    [
                        'kelas_id' => $request->id_kelas,
                        'tahun_ajar_id' => $request->id_tahun_ajar,
                        'mapel_id' => $mapel
                    ],
                    [
                        'jam_mulai' => $request->jam_mulai,
                        'jam_selesai' => $request->jam_selesai
                    ]
                );
            }

            return redirect()->route('mapel_kelas.index')->with('alert', 'Pelajaran berhasil ditambahkan ke kelas!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Pelajaran gagal ditambahkan ke kelas!' . $e->getMessage());
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MapelKelas $mapelKelas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MapelKelas $mapelKelas)
    {
        //
    }
}
