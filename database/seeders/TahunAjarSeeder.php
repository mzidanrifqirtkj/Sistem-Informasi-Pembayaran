<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjar;

class TahunAjarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data tahun ajar contoh
        $tahunAjars = [
            [
                'tahun_ajar' => '2023/2024',
                'start_date' => '2023-07-01',
                'end_date' => '2024-06-30',
                'status' => 'aktif',
            ],
            [
                'tahun_ajar' => '2024/2025',
                'start_date' => '2024-07-01',
                'end_date' => '2025-06-30',
                'status' => 'tidak_aktif',
            ],
        ];

        // Insert data ke tabel tahun_ajars
        foreach ($tahunAjars as $tahunAjar) {
            TahunAjar::firstOrCreate(
                ['tahun_ajar' => $tahunAjar['tahun_ajar']], // Cek berdasarkan tahun_ajar
                $tahunAjar // Data yang akan dibuat jika belum ada
            );
        }

        $this->command->info('Data tahun ajar berhasil ditambahkan!');
    }
}
