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
            'nama_item' => 'Laptop',
            'nominal' => 20000,
            'jumlah' => 1,
        ]);
        TambahanPembayaran::create([
            'nama_item' => 'Motor',
            'nominal' => 15000,
            'jumlah' => 1,
        ]);
        TambahanPembayaran::create([
            'nama_item' => 'Elektronik Lain',
            'nominal' => 5000,
            'jumlah' => 3,
        ]);


        KategoriSantri::create([
            'nama_kategori' => 'Reguler',
            'nominal_syahriyah' => 250000,
        ]);
        KategoriSantri::create([
            'nama_kategori' => 'Fauqo',
            'nominal_syahriyah' => 165000,
        ]);
        KategoriSantri::create([
            'nama_kategori' => 'Dzuriyah',
            'nominal_syahriyah' => 200000,
        ]);
        KategoriSantri::create([
            'nama_kategori' => 'Cuti',
            'nominal_syahriyah' => 125000,
        ]);

    }
}
