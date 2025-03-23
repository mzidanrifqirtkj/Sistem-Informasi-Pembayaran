<?php
namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\TahunAjar;
use Illuminate\Database\Seeder;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua santri
        $santris = Santri::all();

        // Ambil semua kelas dan tahun ajar
        $kelasList = Kelas::all();
        $tahunAjarList = TahunAjar::all();

        if ($santris->isEmpty() || $kelasList->isEmpty() || $tahunAjarList->isEmpty()) {
            $this->command->error('Data santri, kelas, atau tahun ajar tidak ditemukan.');
            return;
        }

        // Loop setiap santri dan buat data absensi
        foreach ($santris as $santri) {
            $kelas1 = $kelasList->random(); // Pilih kelas secara acak
            $kelas2 = $kelasList->random(); // Pilih kelas lain secara acak
            $tahunAjar1 = $tahunAjarList->random(); // Pilih tahun ajar secara acak
            $tahunAjar2 = $tahunAjarList->random(); // Pilih tahun ajar lain secara acak

            $absensis = [
                [
                    'nis' => $santri->nis,
                    'kelas_id' => $kelas1->id_kelas,
                    'tanggal' => '2025-03-01',
                    'status' => 'hadir',
                    'tahun_ajar_id' => $tahunAjar1->id_tahun_ajar,
                ],
                [
                    'nis' => $santri->nis,
                    'kelas_id' => $kelas2->id_kelas,
                    'tanggal' => '2025-03-02',
                    'status' => 'izin',
                    'tahun_ajar_id' => $tahunAjar2->id_tahun_ajar,
                ],
                [
                    'nis' => $santri->nis,
                    'kelas_id' => $kelas1->id_kelas,
                    'tanggal' => '2025-03-03',
                    'status' => 'sakit',
                    'tahun_ajar_id' => $tahunAjar1->id_tahun_ajar,
                ],
                [
                    'nis' => $santri->nis,
                    'kelas_id' => $kelas2->id_kelas,
                    'tanggal' => '2025-03-04',
                    'status' => 'alpa',
                    'tahun_ajar_id' => $tahunAjar2->id_tahun_ajar,
                ],
            ];

            // Insert data ke tabel absensis
            Absensi::insert($absensis);
        }

        $this->command->info('Data absensi untuk semua santri berhasil ditambahkan!');
    }
}
