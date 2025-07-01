<?php
namespace App\Http\Controllers;

use App\Models\RiwayatKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Santri;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Validator;
use Yajra\DataTables\Facades\DataTables;

class RiwayatKelasController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', RiwayatKelas::class);

        $kelas = Kelas::all();
        $mapel = MataPelajaran::all();
        $tahunAjar = TahunAjar::all();

        return view('riwayat-kelas.index', compact('kelas', 'mapel', 'tahunAjar'));
    }

    public function getData(Request $request)
    {
        $this->authorize('viewAny', RiwayatKelas::class);

        $user = auth()->user();
        $query = RiwayatKelas::with([
            'santri',
            'mapelKelas.kelas',
            'mapelKelas.mataPelajaran',
            'mapelKelas.tahunAjar'
        ]);

        // Apply role-based filtering
        if ($user->hasRole('santri') && !$user->santri->is_ustadz) {
            // Santri only sees own data
            $query->where('santri_id', $user->santri->id_santri);
        } elseif ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            // Ustadz sees santri in classes they teach
            $santriPolicy = app(\App\Policies\SantriPolicy::class);
            $santriIds = $santriPolicy->getSantriDiKelasUstadz($user->santri)->pluck('id_santri');
            $query->whereIn('santri_id', $santriIds);
        }

        // Apply filters
        if ($request->kelas_id) {
            $query->whereHas('mapelKelas', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->mapel_id) {
            $query->whereHas('mapelKelas', function ($q) use ($request) {
                $q->where('mapel_id', $request->mapel_id);
            });
        }

        if ($request->tahun_ajar_id) {
            $query->whereHas('mapelKelas', function ($q) use ($request) {
                $q->where('tahun_ajar_id', $request->tahun_ajar_id);
            });
        }

        return DataTables::of($query)
            ->addColumn('santri', fn($row) => $row->santri->nama_santri ?? '-')
            ->addColumn('kelas', fn($row) => $row->mapelKelas->kelas->nama_kelas ?? '-')
            ->addColumn('mapel', fn($row) => $row->mapelKelas->mataPelajaran->nama_mapel ?? '-')
            ->addColumn('tahun_ajar', fn($row) => $row->mapelKelas->tahunAjar->tahun_ajar ?? '-')
            ->addColumn('action', function ($row) {
                $riwayat = RiwayatKelas::find($row->id_riwayat_kelas);
                $actions = '';

                if (auth()->user()->can('update', $riwayat)) {
                    $editUrl = route('riwayat-kelas.edit', $row->id_riwayat_kelas);
                    $actions .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning me-1">Edit</a>';
                }

                if (auth()->user()->can('delete', $riwayat)) {
                    $deleteUrl = route('riwayat-kelas.destroy', $row->id_riwayat_kelas);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    $actions .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;"
    onsubmit="return confirm(\'Yakin hapus data ini?\')">
    ' . $csrf . $method . '
    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
</form>';
                }

                return $actions ?: '<span class="text-muted">No actions</span>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $this->authorize('create', RiwayatKelas::class);

        $santri = Santri::where('is_ustadz', false)->get();
        $mapelKelas = \App\Models\MapelKelas::with(['kelas', 'mataPelajaran', 'tahunAjar'])->get();

        return view('riwayat-kelas.create', compact('santri', 'mapelKelas'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', RiwayatKelas::class);

        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|exists:santris,id_santri',
            'mapel_kelas_id' => 'required|exists:mapel_kelas,id_mapel_kelas',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        RiwayatKelas::create([
            'santri_id' => $request->santri_id,
            'mapel_kelas_id' => $request->mapel_kelas_id,
        ]);

        return redirect()->route('riwayat-kelas.index')->with('success', 'Riwayat Kelas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $riwayat = RiwayatKelas::findOrFail($id);
        $this->authorize('update', $riwayat);

        $santri = Santri::where('is_ustadz', false)->get();
        $mapelKelas = \App\Models\MapelKelas::with(['kelas', 'mataPelajaran', 'tahunAjar'])->get();

        return view('riwayat-kelas.edit', compact('riwayat', 'santri', 'mapelKelas'));
    }

    public function update(Request $request, $id)
    {
        $riwayat = RiwayatKelas::findOrFail($id);
        $this->authorize('update', $riwayat);

        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|exists:santris,id_santri',
            'mapel_kelas_id' => 'required|exists:mapel_kelas,id_mapel_kelas',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $riwayat->update([
            'santri_id' => $request->santri_id,
            'mapel_kelas_id' => $request->mapel_kelas_id,
        ]);

        return redirect()->route('riwayat-kelas.index')->with('success', 'Riwayat Kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $riwayat = RiwayatKelas::findOrFail($id);
        $this->authorize('delete', $riwayat);

        $riwayat->delete();

        return redirect()->route('riwayat-kelas.index')->with('alert', 'Riwayat Kelas berhasil dihapus.');
    }
}
