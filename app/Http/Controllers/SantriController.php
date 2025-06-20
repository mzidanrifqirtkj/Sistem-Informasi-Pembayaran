<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\SantriImport;
use App\Models\KategoriSantri;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SantriController extends Controller
{
    public function index(Request $request)
    {

        $santris = Santri::select('*')
            ->orderBy('created_at', 'desc');
        $keyword = $request->keyword;
        if ($keyword)
            $santris = Santri::where('nama_santri', 'LIKE', "%$keyword%")
                ->orWhere('alamat', 'LIKE', "%$keyword%")
                ->orWhere('no_hp', 'LIKE', "%$keyword%")
                ->latest();
        return view('santri.index', compact('santris'));
    }

    public function getSantri()
    {
        try {
            $santris = Santri::select('id_santri', 'nis', 'nama_santri', 'alamat', 'no_hp');

            return DataTables::of($santris)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('santri.edit', $row->id_santri) . '" class="btn btn-sm btn-info"><i class="fas fa-pen"></i></a>
                            <button class="btn btn-sm btn-danger" onclick="deleteData(' . $row->id_santri . ')"><i class="fas fa-trash"></i></button>';
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
    /**
     * Menampilkan halaman impor data santri.
     */
    public function importForm()
    {
        return view('santri.import');
    }

    /**
     * Menangani proses impor data santri.
     */
    public function import(Request $request)
    {
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori_santris = \App\Models\KategoriBiaya::where('status', 'jalur')->get();
        $users = User::all();
        return view('santri.create', compact('kategori_santris', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // dd($request->all()); // Debugging - hapus setelah selesai
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
                // 'user_id' => 'required|exists:users,id_user|unique:santris',
                //kategori_santri
                'kategori_santri_id' => 'required|exists:kategori_santris,id_kategori_santri',
                // 'status' => 'required|in:aktif,non_aktif',
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
            // ketika baru di create, buat tagihan pendaftaran

            return redirect()->route('santri.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Santri $santri)
    {
        $santri->load('tambahanBulanans', 'user', 'kategoriSantri');
        return view('santri.show', compact('santri'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Santri $santri)
    {
        $kategori_santris = \App\Models\KategoriBiaya::where('status', 'jalur')->get();
        $users = User::all();
        // $kategori_santris = KategoriSantri::all();
        return view('santri.edit', compact('santri', 'kategori_santris', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Santri $santri)
    {
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
                // 'user_id' => 'required|exists:users,id_user|unique:santris,user_id,' . $santri->id_santri . ',id_santri',
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
            // Inisialisasi dataToUpdate
            $dataToUpdate = [];
            foreach ($validated as $key => $value) {
                if ($santri->$key != $value) {
                    $dataToUpdate[$key] = $value;
                }
            }

            if ($request->hasFile('foto')) {
                // Hapus file lama jika ada
                if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
                    Storage::disk('public')->delete($santri->foto);
                }
                // Simpan file baru
                $fotoPath = $request->file('foto')->store('santri/foto', 'public');
                $dataToUpdate['foto'] = $fotoPath;
            }

            if ($request->hasFile('foto_kk')) {
                // Hapus file lama jika ada
                if ($santri->foto_kk && Storage::disk('public')->exists($santri->foto_kk)) {
                    Storage::disk('public')->delete($santri->foto_kk);
                }
                // Simpan file baru
                $fotoKkPath = $request->file('foto_kk')->store('santri/foto_kk', 'public');
                $dataToUpdate['foto_kk'] = $fotoKkPath;
            }

            // Update data jika ada perubahan
            if (!empty($dataToUpdate)) {
                $santri->update($dataToUpdate);
            }

            return redirect()->route('santri.show', $santri)->with('alert', 'Santri updated successfully.');
        } catch (\Exception $e) {
            // Log general exception
            return back()->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Santri $santri)
    {
        $santri->delete();
        return redirect()->route('santri.index')->with('alert', 'Santri deleted successfully.');
    }

}
