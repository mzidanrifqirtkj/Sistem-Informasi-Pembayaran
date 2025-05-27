<?php

namespace App\Http\Controllers;

use App\Models\DaftarBiaya;
use App\Models\KategoriBiaya;
use Illuminate\Http\Request;

class DaftarBiayaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('daftar-biayas.index');
    }

    /**
     * Get data for DataTables
     */
    public function data()
    {
        $daftarBiayas = DaftarBiaya::with('kategoriBiaya')->get();

        return datatables()->of($daftarBiayas)
            ->addColumn('nama_kategori', function ($item) {
                return $item->kategoriBiaya->nama_kategori;
            })
            ->addColumn('status', function ($item) {
                return $item->kategoriBiaya->status;
            })
            ->addColumn('action', function ($item) {
                return '
        <div class="btn-group">
            <a href="' . route('daftar-biayas.edit', $item->id_daftar_biaya) . '" class="btn btn-warning btn-sm">Edit</a>
            <form action="' . route('daftar-biayas.destroy', $item->id_daftar_biaya) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')">
                ' . csrf_field() . '
                ' . method_field('DELETE') . '
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </div>';
            })

            ->rawColumns(['action'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = ['tahunan', 'eksidental', 'tambahan', 'jalur'];
        $kategoris = KategoriBiaya::whereIn('status', $statuses)->get(); // Get all categories initially

        return view('daftar-biayas.create', compact('statuses', 'kategoris'));
    }

    /**
     * Get categories by status
     */
    public function getCategoriesByStatus(Request $request)
    {
        $kategoris = KategoriBiaya::where('status', $request->status)->get();
        return response()->json($kategoris);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_biaya_id' => 'required|exists:kategori_biayas,id_kategori_biaya',
            'nominal' => 'required|numeric|min:0',
        ]);

        DaftarBiaya::create($request->all());

        return redirect()->route('daftar-biayas.index')
            ->with('success', 'Daftar Biaya created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $daftarBiaya = DaftarBiaya::with('kategoriBiaya')->findOrFail($id);

        return view('daftar-biayas.edit', compact('daftarBiaya'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:0',
        ]);

        $daftarBiaya = DaftarBiaya::findOrFail($id);
        $daftarBiaya->update([
            'nominal' => $request->nominal
        ]);

        return redirect()->route('daftar-biayas.index')
            ->with('success', 'Data biaya berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $daftarBiaya = DaftarBiaya::findOrFail($id);
        $daftarBiaya->delete();

        return redirect()->route('daftar-biayas.index')
            ->with('success', 'Daftar Biaya deleted successfully.');
    }
}
