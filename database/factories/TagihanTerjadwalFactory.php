<?php

namespace Database\Factories;

use App\Models\BiayaTerjadwal;
use App\Models\Santri;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TagihanTerjadwal>
 */
class TagihanTerjadwalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $santri = Santri::inRandomOrder()->first();

        if (!$santri) {
            throw new Exception('Santri tidak ditemukan. Harap tambahkan data ke tabel Santri.');
        }
        $biayaTerjadwal = BiayaTerjadwal::inRandomOrder()->first();

        $rincian = [];

        return [
            'santri_id' => $santri->id_santri,
            'biaya_terjadwal_id' => $biayaTerjadwal->id_biaya_terjadwal,
            'nominal' => $biayaTerjadwal->nominal,
            'rincian' => $rincian,
            'tahun' => $this->faker->year(),
            'status' => 'belum_lunas',
        ];
    }
}
