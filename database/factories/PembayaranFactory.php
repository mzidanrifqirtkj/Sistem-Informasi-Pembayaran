<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\TagihanBulanan;
use App\Models\TagihanTerjadwal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pembayaran>
 */
class PembayaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Pembayaran::class;

    public function definition()
    {
        // Pilih jenis tagihan secara acak
        $jenisTagihan = $this->faker->randomElement(['bulanan', 'terjadwal']);

        if ($jenisTagihan === 'bulanan') {
            // Pilih tagihan bulanan dengan status "belum_lunas"
            $tagihan = TagihanBulanan::where('status', 'belum_lunas')->inRandomOrder()->first();

            // Nominal pembayaran (bisa cicilan atau penuh)
            // $nominal = $this->faker->numberBetween(1, $tagihan->nominal);

            //langsung lunas
            $nominal = $tagihan->nominal;

            // Periksa apakah pembayaran ini memenuhi nominal tagihan
            $isLunas = $nominal >= $tagihan->nominal;

            // Jika lunas, ubah status tagihan
            if ($isLunas) {
                $tagihan->update(['status' => 'lunas']);
            }

            return [
                'tagihan_bulanan_id' => $tagihan->id_tagihan_bulanan,
                'tagihan_terjadwal_id' => null, // Tidak ada tagihan terjadwal
                'nominal_pembayaran' => $nominal,
                'tanggal_pembayaran' => $this->faker->dateTimeBetween('-1 year', 'now'),
                'created_by_id' => User::inRandomOrder()->first()->id_user,
            ];
        } else {
            // Pilih tagihan terjadwal dengan status "belum_lunas"
            $tagihan = TagihanTerjadwal::where('status', 'belum_lunas')->inRandomOrder()->first();

            // Nominal pembayaran (bisa cicilan atau penuh)
            // $nominal = $this->faker->numberBetween(1, $tagihan->nominal);

            //langsung lunas
            $nominal = $tagihan->nominal;


            // Periksa apakah pembayaran ini memenuhi nominal tagihan
            $isLunas = $nominal >= $tagihan->nominal;

            // Jika lunas, ubah status tagihan
            if ($isLunas) {
                $tagihan->update(['status' => 'lunas']);
            }

            return [
                'tagihan_bulanan_id' => null, // Tidak ada tagihan bulanan
                'tagihan_terjadwal_id' => $tagihan->id_tagihan_terjadwal,
                'nominal_pembayaran' => $nominal,
                'tanggal_pembayaran' => $this->faker->dateTimeBetween('-1 year', 'now'),
                'created_by_id' => User::inRandomOrder()->first()->id_user,
            ];
        }
    }

}
