<?php

namespace Database\Seeders;

use App\Models\PaketPembayaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaketPembayaran::create([
            'nama_paket' => 'Paket A',
            'nominal' => 1000000,
            'detail_pembayaran' => 'Syahriyah',
        ]);
        PaketPembayaran::create([
            'nama_paket' => 'Paket B',
            'nominal' => 2000000,
            'detail_pembayaran' => 'Syahriyah, HP',
        ]);
        PaketPembayaran::create([
            'nama_paket' => 'Paket C',
            'nominal' => 3000000,
            'detail_pembayaran' => 'Syahriyah, HP, Laptop',
        ],);
    }
}
