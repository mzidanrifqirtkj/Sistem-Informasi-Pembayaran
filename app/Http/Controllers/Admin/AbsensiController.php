<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\AbsensiImport;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Log;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiController extends Controller
{
    public function index()
    {
        $santriList = Santri::select('nama_santri')->get();
        $kelasList = Kelas::select('id_kelas', 'nama_kelas')->get();
        $tahunAjarList = TahunAjar::select('id_tahun_ajar', 'tahun_ajar')->get();

        return view('absensi.index', compact('santriList', 'kelasList', 'tahunAjarList'));
    }

    public function getAbsensi(Request $request)
    {
        try {
            $absensis = Absensi::with(['santri', 'kelas', 'tahunAjar'])
                ->select('id_absensi', 'nis', 'bulan', 'minggu_per_bulan', 'jumlah_hadir', 'jumlah_izin', 'jumlah_sakit', 'jumlah_alpha', 'tahun_ajar_id', 'kelas_id', 'created_at');

            // Filter berdasarkan kelas
            if ($request->kelas) {
                $absensis->whereHas('kelas', function ($query) use ($request) {
                    $query->where('id_kelas', $request->kelas);
                });
            }

            // Filter berdasarkan tahun ajar
            if ($request->tahun_ajar) {
                $absensis->whereHas('tahunAjar', function ($query) use ($request) {
                    $query->where('id_tahun_ajar', $request->tahun_ajar);
                });
            }

            // Filter berdasarkan bulan
            if ($request->bulan) {
                $absensis->where('bulan', $request->bulan);
            }

            // Filter berdasarkan minggu
            if ($request->minggu) {
                $absensis->where('minggu_per_bulan', $request->minggu);
            }

            // Filter berdasarkan nama santri
            if ($request->nama_santri) {
                $absensis->whereHas('santri', function ($query) use ($request) {
                    $query->where('nama_santri', 'like', '%' . $request->nama_santri . '%');
                });
            }


            return datatables()->of($absensis)
                ->addIndexColumn()
                ->addColumn('nama_santri', function ($row) {
                    return $row->santri ? $row->santri->nama_santri : '-';
                })
                ->addColumn('kelas', function ($row) {
                    return $row->kelas ? $row->kelas->nama_kelas : '-';
                })
                ->addColumn('tahun_ajar', function ($row) {
                    return $row->tahunAjar ? $row->tahunAjar->tahun_ajar : '-';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.absensi.edit', $row->id_absensi) . '" class="btn btn-sm btn-info"><i class="fas fa-pen"></i></a>
                            <button class="btn btn-sm btn-danger" data-id="' . $row->id_absensi . '"><i class="fas fa-trash"></i></button>';
                })
                ->filter(function ($instence) use ($request) {
                    if ($request->filled("nama_santri")) {
                        $instence->whereHas('santri', function ($query) use ($request) {
                            $query->where('nama_santri', 'like', '%' . $request->nama_santri . '%');
                        });
                    }
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
        return view('absensi.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new AbsensiImport, $request->file('file'));
            return redirect()->route('admin.absensi.index')->with('success', 'Data absensi berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->route('admin.absensi.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id); // Ambil data absensi berdasarkan ID
        if (!$absensi) {
            return redirect()->route('admin.absensi.index')->with('error', 'Data absensi tidak ditemukan.');
        }

        $santris = Santri::all(); // Ambil semua data santri untuk dropdown
        $kelas = Kelas::all(); // Ambil semua data kelas untuk dropdown
        $tahunAjar = TahunAjar::all(); // Ambil semua data tahun ajar untuk dropdown



        return view('absensi.edit', compact('absensi', 'santris', 'kelas', 'tahunAjar'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nis' => 'required|exists:santris,nis',
            'bulan' => 'required|in:Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec',
            'minggu_per_bulan' => 'required|in:Minggu 1,Minggu 2,Minggu 3,Minggu 4,Minggu 5',
            'jumlah_hadir' => 'required|integer|min:0',
            'jumlah_izin' => 'required|integer|min:0',
            'jumlah_sakit' => 'required|integer|min:0',
            'jumlah_alpha' => 'required|integer|min:0',
            'tahun_ajar_id' => 'required|exists:tahun_ajars,id_tahun_ajar',
            'kelas_id' => 'required|exists:kelas,id_kelas',
        ]);

        // Cari data absensi berdasarkan ID
        $absensi = Absensi::findOrFail($id);

        // Jika data tidak ditemukan, kembalikan pesan error
        if (!$absensi) {
            return redirect()->route('admin.absensi.index')->with('error', 'Data absensi tidak ditemukan.');
        }

        // Cek apakah ada data absensi lain dengan nis, bulan, minggu_per_bulan, dan kelas_id yang sama
        $existingAbsensi = Absensi::where('nis', $request->nis)
            ->where('bulan', $request->bulan)
            ->where('minggu_per_bulan', $request->minggu_per_bulan)
            ->where('kelas_id', $request->kelas_id)
            ->where('tahun_ajar_id', $request->tahun_ajar_id)
            ->first();

        // Jika data sudah ada, kembalikan pesan error
        if ($existingAbsensi) {
            return redirect()->back()->with('alert', 'Data absensi yang sama sudah ada.');
        }

        // Update data absensi
        $absensi->update($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('admin.absensi.index')->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return redirect()->route('admin.absensi.index')->with('alert', 'Data absensi berhasil dihapus.');
    }






}
