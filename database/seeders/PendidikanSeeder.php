<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MataPelajaran;
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


        MataPelajaran::create([
            'nama_mapel' => 'Jurumiyyah',
        ]);
        MataPelajaran::create([
            'nama_mapel' => 'Imrity',
        ]);
        MataPelajaran::create([
            'nama_mapel' => 'Alfiyyah 1',
        ]);
        MataPelajaran::create([
            'nama_mapel' => 'Alfiyyah 2',
        ]);
        MataPelajaran::create([
            'nama_mapel' => 'Bukhori 1',
        ]);
        MataPelajaran::create([
            'nama_mapel' => 'Bukhori 2',
        ]);
        MataPelajaran::create([
            'nama_mapel' => 'Ihya 1',
        ]);
        MataPelajaran::create([
            'nama_mapel' => 'Ihya 2',
        ]);
    }
}
