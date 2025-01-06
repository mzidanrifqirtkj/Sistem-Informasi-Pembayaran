<?php

namespace Database\Seeders;

use App\Models\KategoriSantri;
use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\SantriTambahanPembayaran;
use App\Models\TambahanPembayaran;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call([
        //     PendidikanSeeder::class,
        //     BasePembayaranSeeder::class,
        //     UserSeeder::class,
        // ]);

        SantriTambahanPembayaran::factory(7)->recycle([
            Santri::all(),
            TambahanPembayaran::all(),
        ])->create();
    }
}
