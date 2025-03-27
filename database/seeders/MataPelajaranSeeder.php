<?php

namespace Database\Seeders;

use App\Models\KategoriMapel;
use App\Models\MataPelajaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriMapel = [
            'Nahwu' => ['Jurumiyyah', 'Imrithi', 'Alfiyyah 1 Maqro', 'Alfiyyah 1 Diskusi', 'Alfiyyah 2 Maqro', 'Alfiyyah 2 Diskusi'],
            'Aqidah' => ['Aqidatul Awam', 'Kifayatul Awam'],
            'Shorof' => ['Tasrifan', 'Amsilah At-Tashrifiyyah', 'Qowaid Al-Ilal'],
            'Fiqh' => ['Fathul Qarib', 'Fatul Muin 1', 'Fatul Muin 2'],
            'Hadits' => ['Bukhori 1', 'Bukhori 2', 'Bukhori 3', 'Bukhori 4'],
            'Akhlaq' => ['Ta\'lim Mutaallim', 'Ihya 1', 'Ihya 2', 'Ihya 3', 'Ihya 4'],
        ];

        foreach ($kategoriMapel as $kategori => $mapels) {
            // Ambil atau buat kategori mapel
            $kategoriModel = KategoriMapel::firstOrCreate([
                'nama_kategori_mapel' => $kategori,
            ]);

            foreach ($mapels as $mapel) {
                MataPelajaran::firstOrCreate([
                    'nama_mapel' => $mapel,
                    'kategori_mapel_id' => $kategoriModel->id_kategori_mapel,
                ]);
            }
        }

        $this->command->info('Data mata pelajaran berhasil ditambahkan!');
    }
}
