<?php
namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\TahunAjar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data santri, kelas, dan tahun ajar
        $santri = Santri::first(); // Ambil santri pertama
        $kelas = Kelas::first(); // Ambil kelas pertama
        $tahunAjar = TahunAjar::first(); // Ambil tahun ajar pertama

        // Data absensi contoh
        $absensis = [
            [
                'nis' => $santri->nis,
                'kelas_id' => $kelas->id_kelas,
                'tanggal' => '2023-10-01',
                'status' => 'hadir',
                'tahun_ajar_id' => $tahunAjar->id_tahun_ajar,
            ],
            [
                'nis' => $santri->nis,
                'kelas_id' => $kelas->id_kelas,
                'tanggal' => '2023-10-02',
                'status' => 'izin',
                'tahun_ajar_id' => $tahunAjar->id_tahun_ajar,
            ],
            [
                'nis' => $santri->nis,
                'kelas_id' => $kelas->id_kelas,
                'tanggal' => '2023-10-03',
                'status' => 'sakit',
                'tahun_ajar_id' => $tahunAjar->id_tahun_ajar,
            ],
            [
                'nis' => $santri->nis,
                'kelas_id' => $kelas->id_kelas,
                'tanggal' => '2023-10-04',
                'status' => 'alpa',
                'tahun_ajar_id' => $tahunAjar->id_tahun_ajar,
            ],
        ];

        // Insert data ke tabel absensis
        foreach ($absensis as $absensi) {
            Absensi::create($absensi);
        }

        $this->command->info('Data absensi berhasil ditambahkan!');
    }
}
