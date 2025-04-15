<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AbsensiHarian;
use App\Models\Santri;
use Carbon\Carbon;

class AbsensiHarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua santri yang tersedia
        $santris = Santri::all();

        // Jika tidak ada santri, tampilkan pesan
        if ($santris->isEmpty()) {
            $this->command->warn("Tidak ada data santri. Pastikan tabel santris sudah terisi.");
            return;
        }

        // Generate data absensi harian untuk setiap santri dalam 7 hari terakhir
        foreach ($santris as $santri) {
            for ($i = 0; $i < 7; $i++) {
                AbsensiHarian::firstOrCreate([
                    'santri_id' => $santri->id_santri,
                    'tanggal_hari' => Carbon::now()->subDays($i)->format('Y-m-d'),
                ]);
            }
        }

        $this->command->info('Data absensi harian berhasil ditambahkan!');
    }
}
