<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriSantri;

class KategoriSantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data kategori santri
        $kategoriSantris = [
            [
                'nama_kategori' => 'Santri Baru',
                'nominal_syahriyah' => 500000, // Nominal syahriyah untuk kategori umum
            ],
            [
                'nama_kategori' => 'Ustadz',
                'nominal_syahriyah' => 0, // Nominal syahriyah untuk kategori ustadz (gratis)
            ],
        ];

        // Insert data ke tabel kategori_santris
        foreach ($kategoriSantris as $kategoriSantri) {
            KategoriSantri::firstOrCreate(
                ['nama_kategori' => $kategoriSantri['nama_kategori']], // Cek berdasarkan nama_kategori
                $kategoriSantri // Data yang akan dibuat jika belum ada
            );
        }

        $this->command->info('Data kategori santri berhasil ditambahkan!');
    }
}
