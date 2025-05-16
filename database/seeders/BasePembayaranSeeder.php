<?php

namespace Database\Seeders;

use App\Models\BiayaTerjadwal;
use App\Models\KategoriSantri;
use App\Models\TambahanBulanan;
use Illuminate\Database\Seeder;

class BasePembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TambahanBulanan::create([
            'nama_item' => 'Laptop',
            'nominal' => 20000,
        ]);
        TambahanBulanan::create([
            'nama_item' => 'Motor',
            'nominal' => 15000,
        ]);
        TambahanBulanan::create([
            'nama_item' => 'Elektronik Lain',
            'nominal' => 5000,
        ]);


        // KategoriSantri::create([
        //     'nama_kategori' => 'Reguler',
        //     'nominal_syahriyah' => 250000,
        // ]);
        // KategoriSantri::create([
        //     'nama_kategori' => 'Fauqo',
        //     'nominal_syahriyah' => 165000,
        // ]);
        // KategoriSantri::create([
        //     'nama_kategori' => 'Cuti',
        //     'nominal_syahriyah' => 125000,
        // ]);

        // Buat data biaya terjadwal
        BiayaTerjadwal::create([
            'periode' => 'tahunan',
            'nama_biaya' => 'Pendaftaran Ulang',
            'nominal' => 300000,
        ]);
        BiayaTerjadwal::create([
            'periode' => 'tahunan',
            'nama_biaya' => 'Haflah',
            'nominal' => 400000,
        ]);
        BiayaTerjadwal::create([
            'periode' => 'tahunan',
            'nama_biaya' => 'Ziaroh',
            'nominal' => 500000,
        ]);
        BiayaTerjadwal::create([
            'periode' => 'sekali',
            'nama_biaya' => 'Pendaftaran Santri Baru',
            'nominal' => 2150000,
        ]);
        BiayaTerjadwal::create([
            'periode' => 'tahunan',
            'nama_biaya' => 'Imtihan 1',
            'nominal' => 50000,
        ]);
        BiayaTerjadwal::create([
            'periode' => 'tahunan',
            'nama_biaya' => 'Imtihan 2',
            'nominal' => 50000,
        ]);

    }
}
