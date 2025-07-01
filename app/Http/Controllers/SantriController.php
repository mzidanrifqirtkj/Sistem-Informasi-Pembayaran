<?php
namespace App\Http\Controllers;

use App\Exports\SantriImportTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\SantriImport;
use App\Models\KategoriSantri;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Santri::class);

        $user = auth()->user();

        // Role-based data access
        if ($user->hasRole('santri') && !$user->santri->is_ustadz) {
            // Santri regular redirect to own profile
            return redirect()->route('santri.show', $user->santri->id_santri);
        }

        $query = Santri::select('*')->orderBy('created_at', 'desc');

        // Apply role-based filtering
        if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            // Ustadz can only see santri in classes they teach
            $santriPolicy = app(\App\Policies\SantriPolicy::class);
            $santriIds = $santriPolicy->getSantriDiKelasUstadz($user->santri)->pluck('id_santri');
            $query->whereIn('id_santri', $santriIds);
        }

        // Search functionality
        $keyword = $request->keyword;
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_santri', 'LIKE', "%$keyword%")
                    ->orWhere('alamat', 'LIKE', "%$keyword%")
                    ->orWhere('no_hp', 'LIKE', "%$keyword%")
                    ->orWhere('nis', 'LIKE', "%$keyword%");
            });
        }

        $santris = $query->get();

        return view('santri.index', compact('santris'));
    }

    public function getSantri()
    {
        $this->authorize('viewAny', Santri::class);

        try {
            $user = auth()->user();
            $query = Santri::select('id_santri', 'nis', 'nama_santri', 'alamat', 'no_hp');

            // Apply role-based filtering
            if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
                $santriPolicy = app(\App\Policies\SantriPolicy::class);
                $santriIds = $santriPolicy->getSantriDiKelasUstadz($user->santri)->pluck('id_santri');
                $query->whereIn('id_santri', $santriIds);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actions = '';

                    // View button - check individual permission
                    $santri = Santri::find($row->id_santri);
                    if (auth()->user()->can('view', $santri)) {
                        $actions .= '<a href="' . route('santri.show', $row->id_santri) . '" class="btn btn-sm btn-info me-1"><i
        class="fas fa-eye"></i></a>';
                    }

                    // Edit button - admin only
                    if (auth()->user()->can('update', $santri)) {
                        $actions .= '<a href="' . route('santri.edit', $row->id_santri) . '" class="btn btn-sm btn-warning me-1"><i
        class="fas fa-pen"></i></a>';
                    }

                    // Delete button - admin only
                    if (auth()->user()->can('delete', $santri)) {
                        $actions .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $row->id_santri . ')"><i
        class="fas fa-trash"></i></button>';
                    }

                    return $actions ?: '<span class="text-muted">No actions</span>';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importForm()
    {
        $this->authorize('santri.import');
        return view('santri.import');
    }

    public function import(Request $request)
    {
        $this->authorize('santri.import');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new SantriImport, $request->file('file'));
            return redirect()->route('santri.index')->with('alert', 'Data santri berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->route('santri.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $this->authorize('santri.import');

        $filePath = storage_path('app/public/template_santri.xlsx');
        return response()->download($filePath, 'template_import_santri.xlsx');
    }

    public function create()
    {
        $this->authorize('create', Santri::class);

        $kategori_santris = \App\Models\KategoriBiaya::where('status', 'jalur')->get();
        $users = User::all();
        return view('santri.create', compact('kategori_santris', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Santri::class);

        try {
            $validated = $request->validate([
                'nama_santri' => 'required|max:100',
                'nis' => 'required|integer|unique:santris',
                'nik' => 'required|string|unique:santris',
                'no_kk' => 'required|string',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'tanggal_lahir' => 'required|date',
                'tempat_lahir' => 'required|string',
                'no_hp' => 'required|string',
                'alamat' => 'required|string',
                'golongan_darah' => 'required|string',
                'pendidikan_formal' => 'required|string',
                'pendidikan_non_formal' => 'required|string',
                'foto' => 'nullable|image',
                'foto_kk' => 'nullable|image',
                'tanggal_masuk' => 'required|date',
                'is_ustadz' => 'required|boolean',
                'kategori_santri_id' => 'required|exists:kategori_santris,id_kategori_santri',
                'nama_ayah' => 'required|string',
                'no_hp_ayah' => 'required|string',
                'pekerjaan_ayah' => 'required|string',
                'tempat_lahir_ayah' => 'required|string',
                'tanggal_lahir_ayah' => 'required|date',
                'alamat_ayah' => 'required|string',
                'nama_ibu' => 'required|string',
                'no_hp_ibu' => 'required|string',
                'pekerjaan_ibu' => 'required|string',
                'alamat_ibu' => 'required|string',
                'tempat_lahir_ibu' => 'required|string',
                'tanggal_lahir_ibu' => 'required|date',
                'nama_wali' => 'nullable|string',
                'no_hp_wali' => 'nullable|string',
                'pekerjaan_wali' => 'nullable|string',
                'alamat_wali' => 'nullable|string',
                'tempat_lahir_wali' => 'nullable|string',
                'tanggal_lahir_wali' => 'nullable|date',
            ]);

            Santri::create($validated);

            return redirect()->route('santri.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Santri $santri)
    {
        $this->authorize('view', $santri);

        $santri->load('tambahanBulanans', 'user', 'kategoriSantri');
        return view('santri.show', compact('santri'));
    }

    public function edit(Santri $santri)
    {
        $this->authorize('update', $santri);

        $kategori_santris = \App\Models\KategoriBiaya::where('status', 'jalur')->get();
        $users = User::all();
        return view('santri.edit', compact('santri', 'kategori_santris', 'users'));
    }

    public function update(Request $request, Santri $santri)
    {
        $this->authorize('update', $santri);

        try {
            $validated = $request->validate([
                'nama_santri' => 'required|max:100',
                'nis' => 'required|integer|unique:santris,nis,' . $santri->id_santri . ',id_santri',
                'nik' => 'required|string|unique:santris,nik,' . $santri->id_santri . ',id_santri',
                'no_kk' => 'required|string',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'tanggal_lahir' => 'required|date',
                'tempat_lahir' => 'required|string',
                'no_hp' => 'required|string',
                'alamat' => 'required|string',
                'golongan_darah' => 'required|string',
                'pendidikan_formal' => 'required|string',
                'pendidikan_non_formal' => 'required|string',
                'foto' => 'nullable|image',
                'foto_kk' => 'nullable|image',
                'tanggal_masuk' => 'required|date',
                'is_ustadz' => 'required|boolean',
                'kategori_santri_id' => 'nullable|exists:kategori_santris,id_kategori_santri',
                'nama_ayah' => 'required|string',
                'no_hp_ayah' => 'required|string',
                'pekerjaan_ayah' => 'required|string',
                'tempat_lahir_ayah' => 'required|string',
                'tanggal_lahir_ayah' => 'required|date',
                'alamat_ayah' => 'required|string',
                'nama_ibu' => 'required|string',
                'no_hp_ibu' => 'required|string',
                'pekerjaan_ibu' => 'required|string',
                'alamat_ibu' => 'required|string',
                'tempat_lahir_ibu' => 'required|string',
                'tanggal_lahir_ibu' => 'required|date',
                'nama_wali' => 'nullable|string',
                'no_hp_wali' => 'nullable|string',
                'pekerjaan_wali' => 'nullable|string',
                'alamat_wali' => 'nullable|string',
                'tempat_lahir_wali' => 'nullable|string',
                'tanggal_lahir_wali' => 'nullable|date',
                'status' => 'required|in:aktif,non_aktif',
            ]);

            $validated['kategori_santri_id'] = $validated['kategori_santri_id'] ?? $santri->kategori_santri_id;

            $dataToUpdate = [];
            foreach ($validated as $key => $value) {
                if ($santri->$key != $value) {
                    $dataToUpdate[$key] = $value;
                }
            }

            if ($request->hasFile('foto')) {
                if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
                    Storage::disk('public')->delete($santri->foto);
                }
                $fotoPath = $request->file('foto')->store('santri/foto', 'public');
                $dataToUpdate['foto'] = $fotoPath;
            }

            if ($request->hasFile('foto_kk')) {
                if ($santri->foto_kk && Storage::disk('public')->exists($santri->foto_kk)) {
                    Storage::disk('public')->delete($santri->foto_kk);
                }
                $fotoKkPath = $request->file('foto_kk')->store('santri/foto_kk', 'public');
                $dataToUpdate['foto_kk'] = $fotoKkPath;
            }

            if (!empty($dataToUpdate)) {
                $santri->update($dataToUpdate);
            }

            return redirect()->route('santri.show', $santri)->with('alert', 'Santri updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function destroy(Santri $santri)
    {
        $this->authorize('delete', $santri);

        $santri->delete();
        return redirect()->route('santri.index')->with('alert', 'Santri deleted successfully.');
    }
}
