<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan tahun dan bulan saat ini
        $currentYear = $request->tahun ?? Carbon::now()->year;
        $currentMonth = $request->bulan ?? Carbon::now()->month;

        // Mendapatkan tahun ajar aktif jika tidak ada filter
        $tahunAjar = null;
        if ($request->tahun_ajar_id) {
            $tahunAjar = TahunAjar::find($request->tahun_ajar_id);
        } else {
            $tahunAjar = TahunAjar::where('status', 'aktif')->first();
        }

        if (!$tahunAjar) {
            return redirect()->back()->with('error', 'Tahun Ajar tidak ditemukan');
        }

        // Mendapatkan jumlah hari dalam bulan
        $daysInMonth = Carbon::createFromDate($currentYear, $currentMonth)->daysInMonth;

        // Mendapatkan data kelas untuk filter
        $kelas = Kelas::all();
        $kelasId = $request->kelas ?? null;

        // Filter santri berdasarkan nama jika ada
        $namaSantri = $request->nama ?? '';

        // Mendapatkan semua tahun ajar untuk filter
        $tahunAjars = TahunAjar::all();

        // Query dasar untuk mendapatkan santri dengan filter yang sesuai
        $santriQuery = Santri::query()->where('status', 'aktif');

        // Filter berdasarkan nama santri
        if (!empty($namaSantri)) {
            $santriQuery->where('nama_santri', 'like', '%' . $namaSantri . '%');
        }

        // Filter santri berdasarkan kelas yang dipilih
        if ($kelasId) {
            // Mengambil santri berdasarkan kelas melalui relasi riwayat kelas
            $santriIds = DB::table('absensi')
                ->where('nis', $kelasId)
                ->where('tahun_ajar_id', $tahunAjar->id_tahun_ajar)
                ->pluck('nis');

            $santriQuery->whereIn('nis', $santriIds);
        }

        $santris = $santriQuery->get();

        // Mendapatkan data absensi untuk santri terpilih dalam bulan ini
        $absensis = Absensi::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('tahun_ajar_id', $tahunAjar->id_tahun_ajar)
            ->when($kelasId, function ($query) use ($kelasId) {
                return $query->where('kelas_id', $kelasId);
            })
            ->get()
            ->groupBy('nis')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->tanggal)->format('d');
                });
            });

        // Mendapatkan daftar bulan untuk filter
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Mendapatkan range tahun untuk filter (5 tahun ke belakang dan 5 tahun ke depan)
        $years = range(Carbon::now()->year - 5, Carbon::now()->year + 5);

        // dd([
        //     'currentMonth' => $currentMonth,
        //     'currentYear' => $currentYear,
        //     'kelasId' => $kelasId,
        //     'tahunAjar' => $tahunAjar->id_tahun_ajar
        // ]);

        return view('absensi.index', compact(
            'santris',
            'absensis',
            'daysInMonth',
            'currentMonth',
            'currentYear',
            'kelas',
            'kelasId',
            'namaSantri',
            'tahunAjar',
            'tahunAjars',
            'months',
            'years'
        ));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nis' => 'required',
            'kelas_id' => 'required',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'tahun_ajar_id' => 'required|exists:tahun_ajars,id_tahun_ajar',
        ]);

        // Cek apakah data absensi sudah ada
        $existingAbsensi = Absensi::where('nis', $request->nis)
            ->where('tanggal', $request->tanggal)
            ->where('tahun_ajar_id', $request->tahun_ajar_id)
            ->first();

        if ($existingAbsensi) {
            // Update absensi yang sudah ada
            $existingAbsensi->status = $request->status;
            $existingAbsensi->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Absensi berhasil diperbarui'
            ]);
        } else {
            // Buat absensi baru
            Absensi::create([
                'nis' => $request->nis,
                'kelas_id' => $request->kelas_id,
                'tanggal' => $request->tanggal,
                'status' => $request->status,
                'tahun_ajar_id' => $request->tahun_ajar_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Absensi berhasil disimpan'
            ]);
        }
    }

    public function bulkStore(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required',
            'tahun_ajar_id' => 'required',
            'status' => 'required|array',
            'status.*' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->status as $nis => $status) {
                // Cek apakah data absensi sudah ada
                $existingAbsensi = Absensi::where('nis', $nis)
                    ->where('tanggal', $request->tanggal)
                    ->where('tahun_ajar_id', $request->tahun_ajar_id)
                    ->first();

                if ($existingAbsensi) {
                    // Update absensi yang sudah ada
                    $existingAbsensi->status = $status;
                    $existingAbsensi->save();
                } else {
                    // Buat absensi baru
                    Absensi::create([
                        'nis' => $nis,
                        'kelas_id' => $request->kelas_id,
                        'tanggal' => $request->tanggal,
                        'status' => $status,
                        'tahun_ajar_id' => $request->tahun_ajar_id,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Absensi kelas berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
        }
    }

    public function laporan(Request $request)
    {
        // Mendapatkan tahun dan bulan saat ini
        $currentYear = $request->tahun ?? Carbon::now()->year;
        $currentMonth = $request->bulan ?? Carbon::now()->month;

        // Mendapatkan tahun ajar aktif jika tidak ada filter
        $tahunAjar = null;
        if ($request->tahun_ajar_id) {
            $tahunAjar = TahunAjar::find($request->tahun_ajar_id);
        } else {
            $tahunAjar = TahunAjar::where('status', 'aktif')->first();
        }

        if (!$tahunAjar) {
            return redirect()->back()->with('error', 'Tahun Ajar tidak ditemukan');
        }

        // Filter santri berdasarkan kelas jika ada
        $kelasId = $request->kelas ?? null;
        $namaSantri = $request->nama ?? '';

        // Mendapatkan data kelas dan tahun ajar untuk filter
        $kelas = Kelas::all();
        $tahunAjars = TahunAjar::all();

        // Mendapatkan jumlah hari dalam bulan
        $daysInMonth = Carbon::createFromDate($currentYear, $currentMonth)->daysInMonth;

        // Query dasar untuk mendapatkan santri
        $santriQuery = Santri::query()->where('status', 'aktif');

        // Filter berdasarkan nama santri
        if (!empty($namaSantri)) {
            $santriQuery->where('nama_santri', 'like', '%' . $namaSantri . '%');
        }

        // Filter santri berdasarkan kelas yang dipilih
        if ($kelasId) {
            // Mengambil santri berdasarkan kelas melalui relasi riwayat kelas
            $santriIds = DB::table('riwayat_kelas')
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajar_id', $tahunAjar->id_tahun_ajar)
                ->pluck('santri_id');

            $santriQuery->whereIn('id_santri', $santriIds);
        }

        $santris = $santriQuery->get();

        // Mendapatkan data rekapitulasi absensi untuk tiap santri
        $rekapitulasi = [];

        foreach ($santris as $santri) {
            // Menggunakan relasi yang sudah didefinisikan di model Santri
            $absensiSantri = $santri->absensi()
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('tahun_ajar_id', $tahunAjar->id_tahun_ajar)
                ->get();

            $rekap = [
                'hadir' => $absensiSantri->where('status', 'hadir')->count(),
                'izin' => $absensiSantri->where('status', 'izin')->count(),
                'sakit' => $absensiSantri->where('status', 'sakit')->count(),
                'alpha' => $absensiSantri->where('status', 'alpha')->count(),
                'total' => $absensiSantri->count(),
            ];

            $rekapitulasi[$santri->nis] = $rekap;
        }

        // Mendapatkan daftar bulan untuk filter
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Mendapatkan range tahun untuk filter
        $years = range(Carbon::now()->year - 5, Carbon::now()->year + 5);

        return view('absensi.laporan', compact(
            'santris',
            'rekapitulasi',
            'currentMonth',
            'currentYear',
            'daysInMonth',
            'kelas',
            'kelasId',
            'namaSantri',
            'tahunAjar',
            'tahunAjars',
            'months',
            'years'
        ));
    }

    public function exportPdf(Request $request)
    {
        // Mendapatkan tahun dan bulan saat ini
        $currentYear = $request->tahun ?? Carbon::now()->year;
        $currentMonth = $request->bulan ?? Carbon::now()->month;

        // Mendapatkan tahun ajar
        $tahunAjar = TahunAjar::find($request->tahun_ajar_id) ?? TahunAjar::where('status', 'aktif')->first();

        // Filter berdasarkan kelas
        $kelasId = $request->kelas ?? null;
        $kelas = $kelasId ? Kelas::find($kelasId) : null;

        // Filter santri berdasarkan nama jika ada
        $namaSantri = $request->nama ?? '';

        // Query dasar untuk mendapatkan santri
        $santriQuery = Santri::query()->where('status', 'aktif');

        // Filter berdasarkan nama santri
        if (!empty($namaSantri)) {
            $santriQuery->where('nama_santri', 'like', '%' . $namaSantri . '%');
        }

        // Filter santri berdasarkan kelas yang dipilih
        if ($kelasId) {
            $santriIds = DB::table('riwayat_kelas')
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajar_id', $tahunAjar->id_tahun_ajar)
                ->pluck('santri_id');

            $santriQuery->whereIn('id_santri', $santriIds);
        }

        $santris = $santriQuery->get();

        // Mendapatkan jumlah hari dalam bulan
        $daysInMonth = Carbon::createFromDate($currentYear, $currentMonth)->daysInMonth;

        // Mendapatkan data absensi untuk santri terpilih dalam bulan ini
        $absensis = Absensi::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('tahun_ajar_id', $tahunAjar->id_tahun_ajar)
            ->when($kelasId, function ($query) use ($kelasId) {
                return $query->where('kelas_id', $kelasId);
            })
            ->get()
            ->groupBy('nis')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->tanggal)->format('d');
                });
            });

        // Mendapatkan nama bulan
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $monthName = $months[$currentMonth];

        $namakelas = $kelas ? $kelas->nama_kelas : 'Semua Kelas';

        $pdf = \PDF::loadView('absensi.export', compact(
            'santris',
            'absensis',
            'daysInMonth',
            'currentMonth',
            'currentYear',
            'namakelas',
            'tahunAjar',
            'monthName'
        ));

        return $pdf->download('Rekap-Absensi-' . $namakelas . '-' . $monthName . '-' . $currentYear . '.pdf');
    }
}
