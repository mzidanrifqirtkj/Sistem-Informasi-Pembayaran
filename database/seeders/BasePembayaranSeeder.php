<?php

namespace Database\Seeders;

use App\Models\KategoriSantri;
use App\Models\TambahanPembayaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BasePembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TambahanPembayaran::create([
            'nama_item' => 'HP',
            'nominal' => 10000,
        ]);
        TambahanPembayaran::create([
            'nama_item' => 'Laptop',
            'nominal' => 20000,
        ]);
        TambahanPembayaran::create([
            'nama_item' => 'Motor',
            'nominal' => 30000,
        ]);


        KategoriSantri::create([
            'nama_kategori' => 'Reguler',
            'nominal_syahriyah' => 50000,
        ]);
        KategoriSantri::create([
            'nama_kategori' => 'Fauqo',
            'nominal_syahriyah' => 40000,
        ]);
        KategoriSantri::create([
            'nama_kategori' => 'Dzuriyah',
            'nominal_syahriyah' => 30000,
        ]);

    }
}
