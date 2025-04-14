<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSetiapMapel;
use App\Models\MapelKelas;
use Illuminate\Http\Request;
use App\Models\TahunAjar;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\AbsensiHarian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiController2 extends Controller
{
    protected $months = [
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

    public function index(Request $request)
    {
        // Get active academic year
        $tahunAjar = TahunAjar::where('status', 'aktif')->first();

        if (!$tahunAjar) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajar aktif');
        }

        // Filter parameters
        $namaSantri = $request->input('nama');
        $kelasId = $request->input('kelas');
        $currentMonth = $request->input('bulan') ?? Carbon::now()->month;
        $currentYear = $request->input('tahun') ?? Carbon::now()->year;
        $tahunAjarId = $request->input('tahun_ajar') ?? $tahunAjar->id_tahun_ajar;

        // Get years for filter (current year -1 to current year +1)
        $years = range(now()->year - 1, now()->year + 1);

        // Get all academic years for filter
        $tahunAjars = TahunAjar::all();

        // Get all classes for filter
        $kelas = Kelas::where('tahun_ajar_id', $tahunAjarId)->get();

        // Query for students
        $santriQuery = Santri::query()
            ->when($namaSantri, function ($query, $namaSantri) {
                return $query->where('nama_santri', 'like', "%$namaSantri%");
            })
            ->when($kelasId, function ($query, $kelasId) {
                return $query->where('kelas_id', $kelasId);
            });

        $santris = $santriQuery->get();

        // Get attendance data for the month
        $absensis = [];
        $daysInMonth = Carbon::create($currentYear, $currentMonth, 1)->daysInMonth;

        if ($santris->isNotEmpty()) {
            $absensiData = AbsensiHarian::with('absensiSetiapMapel')
                ->whereBetween('tanggal', [
                    Carbon::create($currentYear, $currentMonth, 1)->format('Y-m-d'),
                    Carbon::create($currentYear, $currentMonth, $daysInMonth)->format('Y-m-d')
                ])
                ->where('tahun_ajar_id', $tahunAjarId)
                ->get();

            foreach ($santris as $santri) {
                foreach ($absensiData as $absensi) {
                    $day = Carbon::parse($absensi->tanggal)->format('d');
                    foreach ($absensi->absensiSetiapMapel as $detail) {
                        if ($detail->santri_id == $santri->id) {
                            $absensis[$santri->nis][$day] = $detail;
                        }
                    }
                }
            }
        }

        return view('absensi.index', [
            'santris' => $santris,
            'absensis' => $absensis,
            'kelas' => $kelas,
            'months' => $this->months,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'years' => $years,
            'tahunAjars' => $tahunAjars,
            'tahunAjar' => $tahunAjar,
            'daysInMonth' => $daysInMonth,
            'namaSantri' => $namaSantri,
            'kelasId' => $kelasId
        ]);
    }

    public function bulkStore(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'tahun_ajar_id' => 'required|exists:tahun_ajar,id_tahun_ajar',
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'status' => 'required|array',
            'status.*' => 'in:hadir,izin,sakit,alpha'
        ]);

        try {
            DB::beginTransaction();

            // Find or create daily attendance record
            $absensiHarian = AbsensiHarian::firstOrCreate([
                'tanggal' => $validatedData['tanggal'],
                'tahun_ajar_id' => $validatedData['tahun_ajar_id']
            ]);

            // Process each student's attendance
            foreach ($validatedData['status'] as $nis => $status) {
                $santri = Santri::where('nis', $nis)->first();

                if ($santri) {
                    // Find or create attendance for each subject in the class
                    $mapelKelas = MapelKelas::where('kelas_id', $validatedData['kelas_id'])->get();

                    foreach ($mapelKelas as $mapel) {
                        AbsensiSetiapMapel::updateOrCreate(
                            [
                                'absensi_harian_id' => $absensiHarian->id,
                                'mata_pelajaran_kelas_id' => $mapel->id,
                                'santri_id' => $santri->id
                            ],
                            [
                                'status' => $status,
                                'jam_mulai' => $mapel->jam_mulai,
                                'jam_selesai' => $mapel->jam_selesai
                            ]
                        );
                    }
                }
            }

            DB::commit();
            return redirect()->route('absensi.index')->with('success', 'Absensi berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    public function laporan()
    {
        // Implementation for report generation
        // return view('absensi.laporan');
    }

    public function export(Request $request)
    {
        // Implementation for PDF export
        // You can use packages like barryvdh/laravel-dompdf or laravel-excel
    }

    private function getIndonesianDayName($dayOfWeek)
    {
        $namaHari = [
            'Minggu',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu'
        ];
        return $namaHari[$dayOfWeek];
    }
}
