<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data kelas contoh
        $kelas = [
            ['nama_kelas' => 'Jurumiyyah'],
            ['nama_kelas' => 'Bukhori'],
            ['nama_kelas' => 'Ihya'],
        ];

        // Insert data ke tabel kelas
        foreach ($kelas as $data) {
            Kelas::create($data);
        }

        $this->command->info('Data kelas berhasil ditambahkan!');
    }
}
