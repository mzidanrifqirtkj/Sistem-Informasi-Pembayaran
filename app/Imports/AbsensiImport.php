<?php

namespace App\Imports;

use App\Models\Absensi;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\TahunAjar;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use DB;

class AbsensiImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        DB::beginTransaction();

        try {
            // Validasi data
            $this->validateRow($row);

            // Cari santri berdasarkan NIS
            $santri = Santri::where('nis', $row['nis'])->firstOrFail();

            // Cari tahun ajar berdasarkan tahun_ajar
            $tahunAjar = TahunAjar::where('tahun_ajar', $row['tahun_ajar'])->firstOrFail();

            // Cari kelas berdasarkan nama_kelas
            $kelas = Kelas::where('nama_kelas', $row['kelas'])->firstOrFail();

            // Konversi format bulan dari panjang ke singkatan
            $bulan = $this->convertBulan($row['bulan']);

            // Cari data absensi yang sudah ada berdasarkan nis, bulan, minggu_per_bulan, dan kelas_id
            $absensi = Absensi::where('nis', $row['nis'])
                ->where('bulan', $bulan)
                ->where('minggu_per_bulan', $row['minggu_per_bulan'])
                ->where('kelas_id', $kelas->id_kelas)
                ->first();

            // Jika data absensi sudah ada, perbarui kolom yang diperlukan
            if ($absensi) {
                $absensi->update([
                    'jumlah_hadir' => (int) $row['jumlah_hadir'],
                    'jumlah_izin' => (int) $row['jumlah_izin'],
                    'jumlah_sakit' => (int) $row['jumlah_sakit'],
                    'jumlah_alpha' => (int) $row['jumlah_alpha'],
                ]);
            } else {
                // Jika data absensi tidak ada, buat data baru
                $absensi = Absensi::create([
                    'nis' => $row['nis'],
                    'bulan' => $bulan,
                    'minggu_per_bulan' => $row['minggu_per_bulan'],
                    'jumlah_hadir' => (int) $row['jumlah_hadir'],
                    'jumlah_izin' => (int) $row['jumlah_izin'],
                    'jumlah_sakit' => (int) $row['jumlah_sakit'],
                    'jumlah_alpha' => (int) $row['jumlah_alpha'],
                    'tahun_ajar_id' => $tahunAjar->id_tahun_ajar,
                    'kelas_id' => $kelas->id_kelas,
                ]);
            }

            DB::commit();
            return $absensi;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validasi data row
     *
     * @param array $row
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRow(array $row)
    {
        $validator = Validator::make($row, [
            'nis' => 'required|exists:santris,nis',
            'bulan' => 'required|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'minggu_per_bulan' => 'required|in:Minggu 1,Minggu 2,Minggu 3,Minggu 4,Minggu 5',
            'jumlah_hadir' => 'required|integer|min:0',
            'jumlah_izin' => 'required|integer|min:0',
            'jumlah_sakit' => 'required|integer|min:0',
            'jumlah_alpha' => 'required|integer|min:0',
            'tahun_ajar' => 'required|exists:tahun_ajars,tahun_ajar',
            'kelas' => 'required|exists:kelas,nama_kelas',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    /**
     * Konversi nama bulan dari panjang ke singkatan
     *
     * @param string $bulan
     * @return string
     */
    private function convertBulan($bulan)
    {
        $bulanPanjang = [
            'Januari' => 'Jan',
            'Februari' => 'Feb',
            'Maret' => 'Mar',
            'April' => 'Apr',
            'Mei' => 'May',
            'Juni' => 'Jun',
            'Juli' => 'Jul',
            'Agustus' => 'Aug',
            'September' => 'Sep',
            'Oktober' => 'Oct',
            'November' => 'Nov',
            'Desember' => 'Dec',
        ];

        return $bulanPanjang[$bulan] ?? $bulan; // Jika tidak ditemukan, kembalikan nilai asli
    }
}
