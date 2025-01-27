<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\PenugasanUstadz;
use App\Models\Santri;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PenugasanUstadzController extends Controller
{
    public function getUstadzs()
    {
        $ustadzs = Santri::where('is_ustadz', 1)->orderBy('nama_santri', 'asc')->get();
        return view('ustadz.data', compact('ustadzs'));
    }

    public function addUstadz()
    {
        $santris = Santri::where('is_ustadz', 0)->orderBy('nama_santri', 'asc')->get();
        return view('ustadz.add', compact('santris'));
    }

    public function storeUstadz(Request $request)
    {
        try {
            $request->validate([
                'santri_id' => 'required|exists:santris,id_santri',
            ]);
            $santri = Santri::findOrFail($request->santri_id);
            $santri->is_ustadz = 1;
            $santri->save();
            return redirect()->route('admin.ustadz.index')->with('alert', 'Ustadz berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.ustadz.index')->with('error', 'Terjadi kesalahan ' . $e->getMessage());
        }
    }

    // public function show(Santri $santri)
    // {
    //     //
    // }

    // public function edit(Santri $santri)
    // {
    //     return view('ustadz.edit', compact('santri'));
    // }

    // public function update(Request $request, Santri $santri)
    // {
    //     try {
    //         $request->validate([
    //             'nama_santri' => 'required|string|max:255',
    //             'is_ustadz' => 'required|boolean',
    //         ]);

    //         $santri->update($request->all());
    //         return redirect()->route('admin.ustadz.index')->with('alert', 'Ustadz berhasil diubah');
    //     } catch (\Exception $e) {
    //         return redirect()->route('admin.ustadz.index')->with('error', 'Terjadi kesalahan');
    //     }
    // }

    // public function destroy(Santri $santri)
    // {
    //     try {
    //         $santri->delete();
    //         return redirect()->route('admin.ustadz.index')->with('alert', 'Ustadz berhasil dihapus');
    //     } catch (\Exception $e) {
    //         return redirect()->route('admin.ustadz.index')->with('error', 'Terjadi kesalahan');
    //     }
    // }

    // public function penugasan()
    // {
    //     $ustadzs = Santri::where('is_ustadz', 1)->orderBy('nama_santri', 'asc')->get();
    //     return view('ustadz.penugasan', compact('ustadzs'));
    // }
    // public function getPenugasan()
    // {
    //     $penugasans = PenugasanUstadz::with(['ustadz', 'mataPelajaran', 'kelas'])->get();

    //     return view('ustadz.penugasan.index', compact('penugasans'));
    // }

    public function getPenugasan(Request $request)
    {
        $penugasan = PenugasanUstadz::with(['kelas', 'tahunAjar', 'ustadz', 'mataPelajaran'])
            ->where('tahun_ajar_id', $request->tahun_ajar_id)
            ->where('kelas_id', $request->kelas_id)
            ->get();

        // return datatables()->of($penugasan)
        //     ->addIndexColumn()
        //     ->addColumn('mata_pelajaran', function($row) {
        //         return $row->nama_mapel; // Dari model Penugasan
        //     })
        //     ->addColumn('guru', function($row) {
        //         $guruOptions = Santri::where('is_ustadz', true)->get();
        //         $select = '<select class="form-control change-guru" data-id="'.$row->id_penugasan.'">';
        //         foreach ($guruOptions as $guru) {
        //             $selected = $guru->id_santri == $row->ustadz_id ? 'selected' : '';
        //             $select .= "<option value='{$guru->id_santri}' {$selected}>{$guru->nama}</option>";
        //         }
        //         $select .= '</select>';
        //         return $select;
        //     })
        //     ->addColumn('aksi', function($row) {
        //         return '<button class="btn btn-danger btn-sm delete-penugasan" data-id="'.$row->id_penugasan.'">Hapus</button>';
        //     })
        //     ->rawColumns(['guru', 'aksi'])
        //     ->make(true);
    }

    /**
     * Menampilkan form untuk menambahkan penugasan baru.
     */
    public function createPenugasan()
    {
        $ustadzs = Santri::where('is_ustadz', 1)->get();
        $mataPelajarans = MataPelajaran::all();
        $kelas = Kelas::all();
        $tahunAjar = TahunAjar::all();

        return view('ustadz.penugasan.create', compact('ustadzs', 'mataPelajarans', 'kelas', 'tahunAjar'));
    }
    public function getPelajaran(Request $request)
    {
        $ustadzs = Santri::where('is_ustadz', 1)->get();
        $idTahunAjar = $request->id_tahun_ajar;
        $idKelas = $request->id_kelas;

        $pelajaran = MataPelajaran::select('id_mapel', 'nama_mapel')
            ->whereHas('penugasanUstadz', function ($query) use ($idTahunAjar, $idKelas) {
                $query->where('kelas_id', $idKelas)->where('tahun_ajar_id', $idTahunAjar);
            })->get();

        return DataTables::of($pelajaran)
            ->addColumn('guru', function ($row) use ($ustadzs) {
                // Buat dropdown untuk memilih guru
                $guruOptions = '';
                foreach ($ustadzs as $guru) {
                    $guruOptions .= '<option value="' . $guru->id_santri . '">' . $guru->nama_santri . '</option>';
                }

                return '<select class="form-control guru-dropdown" data-id="' . $row->id_mapel . '">
                                <option value="">Pilih Guru</option>
                                ' . $guruOptions . '
                            </select>';
            })
            ->rawColumns(['guru']) // Allow HTML in the 'guru' column
            ->make(true);
    }

    /**
     * Menyimpan data penugasan baru.
     */
    public function storePenugasan(Request $request)
    {
        $request->validate([
            'ustadz_id' => 'required|exists:santris,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        PenugasanUstadz::create($request->all());

        return redirect()->route('admin.ustadz.get')->with('success', 'PenugasanUstadz berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit penugasan.
     */
    // public function editPenugasan($id)
    // {
    //     $penugasan = PenugasanUstadz::findOrFail($id);
    //     $ustadzs = Santri::where('is_ustadz', 1)->get();
    //     $mataPelajarans = MataPelajaran::all();

    //     return view('ustadz.penugasan.edit', compact('penugasan', 'ustadzs', 'mataPelajarans'));
    // }

    // /**
    //  * Memperbarui data penugasan.
    //  */
    // public function updatePenugasan(Request $request, $id)
    // {
    //     $request->validate([
    //         'ustadz_id' => 'required|exists:santris,id',
    //         'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
    //         'kelas_id' => 'nullable|exists:kelas,id',
    //     ]);

    //     $penugasan = PenugasanUstadz::findOrFail($id);
    //     $penugasan->update($request->all());

    //     return redirect()->route('admin.ustadz.getPenugasan')->with('success', 'Penugasan ustadz berhasil diperbarui.');
    // }

    // /**
    //  * Menghapus data penugasan.
    //  */
    // public function destroyPenugasan($id)
    // {
    //     $penugasan = PenugasanUstadz::findOrFail($id);
    //     $penugasan->delete();

    //     return redirect()->route('admin.ustadz.getPenugasan')->with('success', 'Penugasan ustadz berhasil dihapus.');
    // }

    public function createMapelKelas()
    {
        $kelas = Kelas::all();
        $tahunAjar = TahunAjar::all();
        $mapel = MataPelajaran::all();

        return view('ustadz.penugasan.create-mapel-kelas', compact('kelas', 'tahunAjar', 'mapel'));
    }

    public function storeMapelKelas(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required',
            'id_tahun_ajar' => 'required',
            'id_mapel.*' => 'required', // Array pelajaran
        ]);

        foreach ($request->id_mapel as $mapel) {
            PenugasanUstadz::updateOrCreate(
                [
                    'kelas_id' => $request->id_kelas,
                    'tahun_ajar_id' => $request->id_tahun_ajar,
                    'mapel_id' => $mapel
                ],
                [] // Update tidak perlu data tambahan
            );
        }

        return back()->with('success', 'Pelajaran berhasil ditambahkan ke kelas!');
    }
}
