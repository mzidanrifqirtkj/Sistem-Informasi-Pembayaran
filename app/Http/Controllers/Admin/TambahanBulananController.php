<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\TambahanBulanan;
use Illuminate\Http\Request;

class TambahanBulananController extends Controller
{
    public function index()
    {
        $itemTambahan = TambahanBulanan::all();
        return view('tambahan-bulanan.index', compact('itemTambahan'));
    }

    public function create()
    {
        return view('tambahan-bulanan.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_item' => 'required|string|max:255',
                'nominal' => 'required|numeric|min:0',
            ]);

            TambahanBulanan::create([
                'nama_item' => $request->nama_item,
                'nominal' => $request->nominal,
            ]);

            return redirect()->route('admin.tambahan_bulanan.index')->with('alert', 'Item berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $item = TambahanBulanan::findOrFail($id);
            // dd($item);
            return view('tambahan-bulanan.edit', compact('item'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $item)
    {
        try {
            $validatedData = $request->validate([
                'nama_item' => 'required|string|max:20',
                'nominal' => 'required|numeric|min:0'
            ]);
            $item = TambahanBulanan::findOrFail($item);
            $item->update($validatedData);
            return redirect()->route('admin.tambahan_bulanan.index')->with('alert', 'Item Berhasil di Edit');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $item = TambahanBulanan::findOrFail($id);
            $item->delete();
            return redirect()->route('admin.tambahan_bulanan.index')->with('alert', 'Item Berhasil di Hapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function itemSantri()
    {
        $santris = Santri::with(['tambahanBulanans', 'kategoriSantri'])->get();
        return view('tambahan-bulanan.item-santri', compact('santris'));
    }

    public function editItemSantri($santri)
    {
        $santri = Santri::findOrFail($santri);
        $santri->load('tambahanBulanans');
        $items = TambahanBulanan::all();
        return view('tambahan-bulanan.item-santri-edit', compact('santri', 'items'));
    }
    public function updateItemSantri(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);
        $items = $request->input('items', []);

        $syncData = [];
        foreach ($items as $itemId => $item) {
            if (isset($item['aktif']) && $item['aktif'] == 1) {
                $syncData[$itemId] = ['jumlah' => $item['jumlah'] ?? 0];
            }
        }


        $santri->tambahanBulanans()->sync($syncData);

        return redirect()->route('admin.tambahan_bulanan.item_santri')->with('alert', 'Data berhasil diperbarui.');
    }
}
