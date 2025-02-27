<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('absensis')->insert([
            [
                'nis' => '201001',
                'jumlah_hadir' => 3,
                'jumlah_izin' => 30,
                'jumlah_sakit' => 10,
                'jumlah_alpha' => 30,
                'bulan' => 'Sep',
                'minggu_per_bulan' => 'Minggu 2',
                'tahun_ajar_id' => 8,
                'kelas_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nis' => '1004',
                'jumlah_hadir' => 1,
                'jumlah_izin' => 1,
                'jumlah_sakit' => 1,
                'jumlah_alpha' => 1,
                'bulan' => 'Jan',
                'minggu_per_bulan' => 'Minggu 2',
                'tahun_ajar_id' => 8,
                'kelas_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nis' => '201001',
                'jumlah_hadir' => 1,
                'jumlah_izin' => 1,
                'jumlah_sakit' => 1,
                'jumlah_alpha' => 1,
                'bulan' => 'Jan',
                'minggu_per_bulan' => 'Minggu 1',
                'tahun_ajar_id' => 9,
                'kelas_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
