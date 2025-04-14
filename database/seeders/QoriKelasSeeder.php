<?php

namespace Database\Seeders;

use App\Models\MapelKelas;
use App\Models\QoriKelas;
use App\Models\Santri;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QoriKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua ustadz dari tabel santris yang memiliki is_ustadz = true
        $ustadzList = Santri::where('is_ustadz', true)->get();
        if ($ustadzList->isEmpty()) {
            $this->command->warn('Tidak ada data ustadz di tabel santris! Seeder dibatalkan.');
            return;
        }

        // Ambil semua mapel_kelas
        $mapelKelasList = MapelKelas::all();
        if ($mapelKelasList->isEmpty()) {
            $this->command->warn('Tidak ada data mata pelajaran kelas! Seeder dibatalkan.');
            return;
        }

        // Loop setiap mapel_kelas dan assign qori (ustadz) secara acak
        foreach ($mapelKelasList as $mapelKelas) {
            QoriKelas::firstOrCreate([
                'ustadz_id' => $ustadzList->random()->id_santri, // Pilih ustadz secara acak
                'mapel_kelas_id' => $mapelKelas->id_mapel_kelas,
            ]);
        }

        $this->command->info('Seeder qori_kelas berhasil ditambahkan!');
    }
}
