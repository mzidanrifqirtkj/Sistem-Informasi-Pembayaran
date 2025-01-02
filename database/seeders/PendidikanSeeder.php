<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\TahunAjar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //tahun ajar
        TahunAjar::create([
            'tahun_ajar' => '2021/2022',
        ]);
        TahunAjar::create([
            'tahun_ajar' => '2022/2023',
        ]);
        TahunAjar::create([
            'tahun_ajar' => '2023/2024',
        ]);
        TahunAjar::create([
            'tahun_ajar' => '2023/2025',
        ]);


        //kelas
        Kelas::create([
            'nama_kelas' => 'Jurumiyyah',
        ]);
        Kelas::create([
            'nama_kelas' => 'Imrity',
        ]);
        Kelas::create([
            'nama_kelas' => 'Alfiyyah 1',
        ]);
        Kelas::create([
            'nama_kelas' => 'Alfiyyah 2',
        ]);
        Kelas::create([
            'nama_kelas' => 'Bukhori',
        ]);
        Kelas::create([
            'nama_kelas' => 'Ihya',
        ]);

    }
}
