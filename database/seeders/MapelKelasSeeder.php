<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MapelKelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MapelKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data dari database
        $tahunAjars = TahunAjar::all(); // Ambil semua tahun ajar
        $kelasList = Kelas::all(); // Ambil semua kelas
        $mapelList = MataPelajaran::all(); // Ambil semua mata pelajaran

        foreach ($kelasList as $kelas) {
            foreach ($mapelList as $mapel) {
                foreach ($tahunAjars as $tahunAjar) {
                    MapelKelas::firstOrCreate([
                        'kelas_id' => $kelas->id_kelas,
                        'mapel_id' => $mapel->id_mapel,
                        'tahun_ajar_id' => $tahunAjar->id_tahun_ajar,
                    ]);
                }
            }
        }

        $this->command->info('Data Mapel Kelas berhasil ditambahkan!');

    }
}
