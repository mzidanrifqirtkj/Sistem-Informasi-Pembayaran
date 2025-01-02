<?php

namespace Database\Seeders;

use App\Models\PaketPembayaran;
use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PendidikanSeeder::class,
            PembayaranSeeder::class,
        ]);
        Santri::factory(10)->recycle([
            PaketPembayaran::all(),
            User::all(),
        ])->create();







    }
}
