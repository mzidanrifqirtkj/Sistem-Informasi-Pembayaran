<?php

namespace App\Http\Controllers;

use App\Models\QoriKelas;
use App\Models\Santri;
use Illuminate\Http\Request;
use Log;

class QoriKelasController extends Controller
{
    public function index()
    {
        $qoriKelas = QoriKelas::with('santri')->get();
        return view('qori_kelas.index', compact('qoriKelas'));
    }

    public function toggleStatus($id)
    {
        $qori = QoriKelas::where('id_qori_kelas', $id)->firstOrFail();

        // Toggle status otomatis
        $newStatus = $qori->status === 'aktif' ? 'non_aktif' : 'aktif';
        $qori->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Status berhasil diubah menjadi ' . ucfirst($newStatus));
    }



    public function destroy($id)
    {
        $qori = QoriKelas::findOrFail($id);
        $qori->delete();

        return redirect()->route('qori_kelas.index')->with('success', 'Data berhasil dihapus');
    }

    public function generateFromSantri()
    {
        try {
            // Get all ustadz from santri table
            $ustadzs = Santri::where('is_ustadz', true)->get();

            if ($ustadzs->isEmpty()) {
                return redirect()->route('qori_kelas.index')
                    ->with('warning', 'Tidak ada data santri yang merupakan ustadz.');
            }

            $generatedCount = 0;

            foreach ($ustadzs as $ustadz) {
                // Check if the nis already exists in qori_kelas
                $exists = QoriKelas::where('nis', $ustadz->nis)->exists();

                if (!$exists) {
                    QoriKelas::create([
                        'nis' => $ustadz->nis,
                        'status' => 'aktif'
                    ]);
                    $generatedCount++;
                }
            }

            return redirect()->route('qori_kelas.index')
                ->with('success', "Berhasil menambahkan $generatedCount qori baru.");

        } catch (\Exception $e) {
            Log::error('Error generating qori: ' . $e->getMessage());
            return redirect()->route('qori_kelas.index')
                ->with('error', 'Terjadi kesalahan saat generate data qori.');
        }
    }
}
