<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MapelKelas;
use App\Models\MataPelajaran;
use App\Models\QoriKelas;
use App\Models\Santri;
use App\Models\TahunAjar;
use App\Models\WaliKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use function Illuminate\Log\log;

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
            return redirect()->route('admin.ustadz.get')->with('alert', 'Ustadz berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.ustadz.get')->with('error', 'Terjadi kesalahan ' . $e->getMessage());
        }
    }

    // Menampilkan halaman index dengan dropdown tahun ajar
    public function index()
    {
        // Ambil semua tahun ajar
        $tahunAjar = TahunAjar::all();
        // Ambil tahun ajar default (status = aktif)
        $defaultTahun = TahunAjar::where('status', 'aktif')->first();

        return view('ustadz.penugasan.index', compact('tahunAjar', 'defaultTahun'));
    }
    public function getWaliKelas(Request $request)
    {
        $idTahunAjar = $request->id_tahun_ajar;

        // Buat query awal dengan eager load relasi
        $query = WaliKelas::with(['kelas', 'ustadz'])
        ->where('tahun_ajar_id', $idTahunAjar);

        return \Yajra\DataTables\Facades\DataTables::of($query)
            ->filter(function ($query) use ($request) {
                // Cek apakah terdapat query search
                if ($search = $request->input('search.value')) {
                    // Gabungkan pencarian untuk nama kelas dan nama ustadz
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('kelas', function ($q2) use ($search) {
                            $q2->where('nama_kelas', 'like', "%{$search}%");
                        })
                            ->orWhereHas('ustadz', function ($q3) use ($search) {
                                $q3->where('nama_santri', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->addIndexColumn()
            ->addColumn('nama_kelas', function ($row) {
                return $row->kelas->nama_kelas ?? '-';
            })
            ->addColumn('nama_ustadz', function ($row) {
                return $row->ustadz->nama_santri ?? '-';
            })
            ->rawColumns(['nama_kelas', 'nama_ustadz'])
            ->make(true);
    }

    public function getQori(Request $request)
    {
        $idTahunAjar = $request->id_tahun_ajar;

        $query = \App\Models\QoriKelas::with([
            'mapelKelas.mataPelajaran',
            'mapelKelas.kelas',
            'ustadz'
        ])
            ->whereHas('mapelKelas', function ($q) use ($idTahunAjar) {
                $q->where('tahun_ajar_id', $idTahunAjar);
            });

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search.value')) {
                    $query->where(function ($q) use ($search) {
                        // Cari pada relasi mapelKelas -> kelas
                        $q->whereHas('mapelKelas.kelas', function ($q2) use ($search) {
                            $q2->where('nama_kelas', 'like', "%{$search}%");
                        })
                            // Cari pada relasi mapelKelas -> mataPelajaran
                            ->orWhereHas('mapelKelas.mataPelajaran', function ($q3) use ($search) {
                                $q3->where('nama_mapel', 'like', "%{$search}%");
                            })
                            // Cari pada relasi ustadz
                            ->orWhereHas('ustadz', function ($q4) use ($search) {
                                $q4->where('nama_santri', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->addIndexColumn()
            ->addColumn('nama_mapel', function ($row) {
                return $row->mapelKelas->mataPelajaran->nama_mapel ?? '-';
            })
            ->addColumn('nama_kelas', function ($row) {
                return $row->mapelKelas->kelas->nama_kelas ?? '-';
            })
            ->addColumn('nama_ustadz', function ($row) {
                return $row->ustadz->nama_santri ?? '-';
            })
            ->rawColumns(['nama_mapel', 'nama_kelas', 'nama_ustadz'])
            ->make(true);
    }


    public function createQori()
    {
        $ustadzs = Santri::where('is_ustadz', true)->get();
        $kelas = Kelas::all();
        $tahunAjar = TahunAjar::all();

        return view('ustadz.penugasan.create-qori', compact('ustadzs', 'kelas', 'tahunAjar'));
    }

    public function getPelajaran(Request $request)
    {
        $idTahunAjar = $request->id_tahun_ajar;
        $idKelas     = $request->id_kelas;

        // Ambil daftar ustadz (santri dengan is_ustadz true)
        $ustadzs = Santri::where('is_ustadz', true)->get();

        // Ambil data MapelKelas berdasarkan kelas dan tahun ajar, beserta relasi mataPelajaran dan qoriKelas.ustadz
        $pelajaran = MapelKelas::where('kelas_id', $idKelas)
            ->where('tahun_ajar_id', $idTahunAjar)
            ->with('mataPelajaran', 'qoriKelas.ustadz')
            ->get();

        return DataTables::of($pelajaran)
            ->addColumn('mapel_kelas_id', function ($row) {
                Log::debug("data mapel kelas dari datatables : " . print_r($row, true));
                return $row->id_mapel_kelas; // Gunakan id MapelKelas sebagai identifikasi
            })
            ->addColumn('ustadz_dropdown', function ($row) use ($ustadzs) {
                // Dapatkan ustadz yang sudah ditugaskan (jika ada) dari relasi qoriKelas
                $selectedUstadzId = null;
                $qori = $row->qoriKelas->first();
                if ($qori && $qori->ustadz) {
                    $selectedUstadzId = $qori->ustadz->id_santri;
                }

                // Bangun opsi-opsi untuk dropdown
                $options = '<option value="">Pilih Ustadz</option>';
                foreach ($ustadzs as $ustadz) {
                    $selected = ($selectedUstadzId == $ustadz->id_santri) ? 'selected' : '';
                    $options .= '<option value="' . $ustadz->id_santri . '" ' . $selected . '>' . $ustadz->nama_santri . '</option>';
                }

            // Bungkus opsi dalam elemen select, sertakan data-mapel-kelas-id untuk keperluan identifikasi
            return "<select class='form-control ustadz-dropdown' data-mapel-kelas-id='{$row->id_mapel_kelas}'> $options  </select>";
            })
            ->rawColumns(['ustadz_dropdown'])
            ->toJson();
    }

    public function storeQori(Request $request)
    {
        $penugasanData = $request->input('penugasan');
        //cek apakah ustadz null atau tidak, jika null maka delete

        // Simpan data penugasan
        $errors = [];
        foreach ($penugasanData as $data) {
            $mapelKelasId = $data['mapel_kelas_id'];
            $ustadzId = isset($data['ustadz_id']) ? trim($data['ustadz_id']) : null;

            if($ustadzId === '' || $ustadzId === null){
                $deleted = QoriKelas::where('mapel_kelas_id', $mapelKelasId)->delete();
                Log::debug("Deleted qori kelas for mapel_kelas_id {$mapelKelasId}. Deleted: {$deleted}");
                continue;
            }

            //cek existing qori di mapelkelas lain
            $existing = QoriKelas::where('ustadz_id', $ustadzId)
                ->where('mapel_kelas_id', '!=', $mapelKelasId)
                ->first();
            if($existing){
                $errors[] = "Ustadz dengan ID {$ustadzId} sudah menjadi qori untuk mata pelajaran lain.";
                continue;
            }

            QoriKelas::updateOrCreate(
                [
                    'mapel_kelas_id' => $mapelKelasId
                ],
                [
                    'ustadz_id' => $ustadzId
                ]
            );
        }

        if (!empty($errors)) {
            return response()->json([
                'error'    => true,
                'messages' => $errors
            ], 400);
        }
        
        return response()->json(['message' => 'Penugasan berhasil disimpan!']);
    }

    public function createMustahiq()
    {
        $ustadzs = Santri::where('is_ustadz', true)->get();
        $tahunAjar = TahunAjar::all();

        return view('ustadz.penugasan.create-mustahiq', compact('ustadzs', 'tahunAjar'));
    }
    public function getKelas(Request $request)
    {
        try {
            $idTahunAjar = $request->id_tahun_ajar;

            $query = Kelas::with(['waliKelas' => function ($query) use ($idTahunAjar) {
                $query->where('tahun_ajar_id', $idTahunAjar);
            }, 'waliKelas.ustadz']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('ustadz_dropdown', function ($row) {
                    // Ambil semua data santri yang merupakan ustadz
                    $ustadzs = Santri::where('is_ustadz', true)->get();
                    // Jika wali kelas sudah ada, ambil id ustadz-nya, jika tidak null
                    $selectedUstadzId = $row->waliKelas ? $row->waliKelas->ustadz_id : null;
                    $options = '<option value="">Pilih Ustadz</option>';

                foreach ($ustadzs as $ustadz) {
                    $selected = ($selectedUstadzId == $ustadz->id_santri) ? 'selected' : '';
                    $options .= "<option value='{$ustadz->id_santri}' {$selected}>{$ustadz->nama_santri}</option>";
                }

                    return "<select class='form-control ustadz-dropdown' data-kelas-id='{$row->id_kelas}'>$options</select>";
                })
                ->rawColumns(['ustadz_dropdown'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function storeMustahiq(Request $request)
    {
        $idTahunAjar = $request->id_tahun_ajar;
        $penugasanData = $request->penugasan; // Array berisi data: ['kelas_id' => ..., 'ustadz_id' => ...]
        $errors = [];
        foreach ($penugasanData as $data) {
            $kelasId = $data['kelas_id'];
            $ustadzId = isset($data['ustadz_id']) ? trim($data['ustadz_id']) : null;

            if ($ustadzId === '' || $ustadzId === null) {
                $deleted = WaliKelas::where('kelas_id', $kelasId)
                    ->where('tahun_ajar_id', $idTahunAjar)
                    ->delete();
                Log::debug("Deleted wali kelas for kelas_id {$kelasId} in tahun_ajar {$idTahunAjar}. Deleted: {$deleted}");
                continue;
            }
            // Cek apakah ustadz sudah menjadi wali untuk kelas lain di tahun ajaran ini
            $existing = WaliKelas::where('ustadz_id', $ustadzId)
                ->where('tahun_ajar_id', $idTahunAjar)
                ->where('kelas_id', '!=', $kelasId)
                ->first();

            if ($existing) {
                // Tambahkan pesan error jika ditemukan duplikasi
                $errors[] = "Ustadz dengan ID {$ustadzId} sudah menjadi wali untuk kelas lain di tahun ajaran ini.";
                continue; // Lewati proses updateOrCreate untuk data ini
            }

            // updateOrCreate: jika sudah ada wali untuk kelas & tahun tersebut, update ustadz-nya; jika tidak, buat baru
            WaliKelas::updateOrCreate(
                [
                    'kelas_id'      => $kelasId,
                    'tahun_ajar_id' => $idTahunAjar,
                ],
                [
                    'ustadz_id'     => $ustadzId,
                ]
            );
        }

        if (!empty($errors)) {
            return response()->json([
                'error'    => true,
                'messages' => $errors
            ], 400);
        }

        return response()->json(['message' => 'Penugasan Wali Kelas berhasil disimpan!']);
    }



}
