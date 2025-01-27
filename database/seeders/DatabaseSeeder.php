<?php

namespace Database\Seeders;

use App\Models\KategoriSantri;
use App\Models\Santri;
use App\Models\SantriTambahanBulanan;
use App\Models\TambahanBulanan;
use App\Models\User;
use Exception;
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
            BasePembayaranSeeder::class,
            // UserSeeder::class, // jika ingin membuat dummy user
        ]);

        // jika ingin membuat dummy santri
        // Santri::factory(10)->recycle([
        //     KategoriSantri::all(),
        //     User::all()
        // ])->create();

        // SantriTambahanBulanan::factory(3)->recycle([
        //     Santri::all(),
        //     TambahanBulanan::all(),
        // ])->create();

        // $santris = Santri::all();
        // $tambahanPembayarans = TambahanPembayaran::all();
        // $uniqueCombinations = $santris->crossJoin($tambahanPembayarans)->unique(function ($pair) {
        //     return $pair[0]->id . '-' . $pair[1]->id;
        // });
        // foreach ($uniqueCombinations as $combination) {
        //     [$santri, $tambahan] = $combination;
        //     // Factory untuk data pivot
        //     SantriTambahanPembayaran::factory()->create([
        //         'santri_id' => $santri->id,
        //         'tambahan_pembayaran_id' => $tambahan->id,
        //     ]);
        // }


        // // Buat data tagihan
        // TagihanBulanan::factory(20)->recycle([
        //     Santri::all(),
        // ])->create();

        // TagihanTerjadwal::factory(20)->recycle([
        //     Santri::all(),
        //     BiayaTerjadwal::all(),
        // ])->create();

        // // Buat data pembayaran
        // Pembayaran::factory(10)->recycle([
        //     TagihanBulanan::all(),
        //     TagihanTerjadwal::all(),
        //     User::all()->where('role', 'admin'),
        // ])->create();


    }
}
