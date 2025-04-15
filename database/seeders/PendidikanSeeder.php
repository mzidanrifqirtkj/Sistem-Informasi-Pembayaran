<?php

namespace Database\Seeders;

use App\Models\{KategoriMapel, Kelas, MapelKelas, MataPelajaran, TahunAjar};
use Illuminate\Database\Seeder;

class PendidikanSeeder extends Seeder
{
    public function run(): void
    {
        // Tahun Ajar
        // $tahunAjarIds = collect(['2021/2022', '2022/2023', '2023/2024', '2024/2025'])
        // ->map(fn($tahun) => TahunAjar::create(['tahun_ajar' => $tahun])->id_tahun_ajar);

        // Kelas
        // $kelasIds = collect(['Jurumiyyah', 'Imrity', 'Alfiyyah 1', 'Alfiyyah 2', 'Bukhori', 'Ihya'])
        // ->mapWithKeys(fn($kelas) => [$kelas => Kelas::create(['nama_kelas' => $kelas])->id_kelas]);

        // Kategori dan Mata Pelajaran
        // $kategoriMapel = [
        //     'Nahwu' => ['Jurumiyyah', 'Imrithi', 'Alfiyyah 1 Maqro', 'Alfiyyah 1 Diskusi', 'Alfiyyah 2 Maqro', 'Alfiyyah 2 Diskusi'],
        //     'Aqidah' => ['Aqidatul Awam', 'Kifayatul Awam'],
        //     'Shorof' => ['Tasrifan', 'Amsilah At-Tashrifiyyah', 'Qowaid Al-Ilal'],
        //     'Fiqh' => ['Fathul Qarib', 'Fatul Muin 1', 'Fatul Muin 2'],
        //     'Hadits' => ['Bukhori 1', 'Bukhori 2', 'Bukhori 3', 'Bukhori 4'],
        //     'Akhlaq' => ['Ta\'lim Mutaallim', 'Ihya 1', 'Ihya 2', 'Ihya 3', 'Ihya 4'],
        // ];

        //     $mapelIds = collect($kategoriMapel)->mapWithKeys(function ($mapelList, $kategori) {
        //         $kategoriId = KategoriMapel::firstOrCreate(['nama_kategori_mapel' => $kategori])->id_kategori_mapel;
        //         return collect($mapelList)->mapWithKeys(fn($mapel) => [$mapel => MataPelajaran::create(['nama_mapel' => $mapel, 'kategori_mapel_id' => $kategoriId])->id_mapel]);
        //     });

        //     // Mapel Kelas
        //     $mapelPerKelas = [
        //         'Jurumiyyah' => ['Jurumiyyah', 'Tasrifan'],
        //         'Imrity' => ['Imrithi', 'Aqidatul Awam'],
        //         'Alfiyyah 1' => ['Alfiyyah 1 Maqro', 'Alfiyyah 1 Diskusi'],
        //         'Alfiyyah 2' => ['Alfiyyah 2 Maqro', 'Alfiyyah 2 Diskusi'],
        //         'Bukhori' => ['Bukhori 1', 'Bukhori 2', 'Bukhori 3'],
        //         'Ihya' => ['Ihya 1', 'Ihya 2', 'Ihya 3'],
        //     ];

        //     foreach ($mapelPerKelas as $kelasName => $mapelList) {
        //         foreach ($mapelList as $mapelName) {
        //             foreach ($tahunAjarIds as $tahunAjarId) {
        //                 MapelKelas::create([
        //                     'kelas_id' => $kelasIds[$kelasName],
        //                     'mapel_id' => $mapelIds[$mapelName],
        //                     'tahun_ajar_id' => $tahunAjarId,
        //                 ]);
        //             }
        //         }
        //     }
    }
}
