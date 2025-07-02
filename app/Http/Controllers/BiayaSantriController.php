<?php

namespace App\Http\Controllers;

use App\Models\BiayaSantri;
use App\Models\DaftarBiaya;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiayaSantriController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', BiayaSantri::class);

        $user = Auth::user();

        // Jika user adalah santri, hanya tampilkan data dirinya sendiri
        if ($user->hasRole('santri')) {
            $santri = $user->santri;

            if (!$santri) {
                abort(403, 'Data santri tidak ditemukan untuk akun ini.');
            }

            $santri->load('biayaSantris.daftarBiaya.kategoriBiaya');
            $santri->total_biaya = $santri->biayaSantris->sum(function ($biaya) {
                return $biaya->daftarBiaya->nominal * $biaya->jumlah;
            });

            $santris = collect([$santri]); // agar bisa dipakai di view yang sama
        } else {
            // Admin atau ustadz bisa melihat semua santri
            $santris = Santri::with(['biayaSantris.daftarBiaya.kategoriBiaya'])
                ->whereHas('biayaSantris')
                ->get()
                ->map(function ($santri) {
                    $santri->total_biaya = $santri->biayaSantris->sum(function ($biaya) {
                        return $biaya->daftarBiaya->nominal * $biaya->jumlah;
                    });
                    return $santri;
                });
        }

        return view('biaya-santris.index', compact('santris'));
    }

    public function create()
    {
        $this->authorize('create', BiayaSantri::class);

        $daftarBiayas = DaftarBiaya::with('kategoriBiaya')->get();
        $santris = Santri::orderBy('nama_santri', 'asc')->get();

        return view('biaya-santris.create', compact('daftarBiayas', 'santris'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', BiayaSantri::class);

        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri',
            'biaya' => 'required|array',
            'biaya.*.id' => 'required|exists:daftar_biayas,id_daftar_biaya',
            'biaya.*.jumlah' => 'required|numeric|min:1',
        ]);

        foreach ($request->biaya as $item) {
            BiayaSantri::create([
                'santri_id' => $request->santri_id,
                'daftar_biaya_id' => $item['id'],
                'jumlah' => $item['jumlah'],
            ]);
        }

        return redirect()->route('biaya-santris.index')->with('success', 'Biaya santri berhasil ditambahkan');
    }

    public function searchSantri(Request $request)
    {
        $this->authorize('viewAny', BiayaSantri::class);

        $search = $request->q;
        $santris = Santri::where('nama_santri', 'like', "%$search%")->get();

        return response()->json($santris);
    }

    public function searchBiaya(Request $request)
    {
        $this->authorize('viewAny', BiayaSantri::class);

        $search = $request->q;
        $query = DaftarBiaya::with('kategoriBiaya');

        if (!empty($search)) {
            $query->whereHas('kategoriBiaya', function ($q) use ($search) {
                $q->where('nama_kategori', 'like', "%$search%");
            });
        }

        return response()->json($query->get());
    }

    public function show($id)
    {
        $santri = Santri::with(['biayaSantris.daftarBiaya.kategoriBiaya'])->findOrFail($id);

        $this->authorize('view', $santri->biayaSantris->first() ?? BiayaSantri::class);

        $totalBiaya = $santri->biayaSantris->sum(function ($biaya) {
            return $biaya->daftarBiaya->nominal * $biaya->jumlah;
        });

        return view('biaya-santris.show', compact('santri', 'totalBiaya'));
    }

    public function edit($id)
    {
        $biaya = BiayaSantri::where('santri_id', $id)->firstOrFail();

        $this->authorize('update', $biaya);

        $santri = Santri::with(['biayaSantris.daftarBiaya.kategoriBiaya'])->findOrFail($id);
        $daftarBiayas = DaftarBiaya::with('kategoriBiaya')->get();
        $santris = Santri::all();

        return view('biaya-santris.edit', compact('santri', 'daftarBiayas', 'santris'));
    }

    public function update(Request $request, $id)
    {
        $biaya = BiayaSantri::where('santri_id', $id)->firstOrFail();
        $this->authorize('update', $biaya);

        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri',
            'biaya' => 'required|array',
            'biaya.*.id' => 'required|exists:daftar_biayas,id_daftar_biaya',
            'biaya.*.jumlah' => 'required|numeric|min:1',
        ]);

        BiayaSantri::where('santri_id', $id)->delete();

        foreach ($request->biaya as $item) {
            BiayaSantri::create([
                'santri_id' => $request->santri_id,
                'daftar_biaya_id' => $item['id'],
                'jumlah' => $item['jumlah'],
            ]);
        }

        return redirect()->route('biaya-santris.show', $request->santri_id)
            ->with('success', 'Paket biaya santri berhasil diperbarui');
    }

    public function destroy($id)
    {
        $biaya = BiayaSantri::where('santri_id', $id)->firstOrFail();
        $this->authorize('delete', $biaya);

        BiayaSantri::where('santri_id', $id)->delete();

        return redirect()->route('biaya-santris.index')
            ->with('success', 'Paket biaya santri berhasil dihapus');
    }
}
