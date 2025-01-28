<?php

namespace Database\Factories;

use App\Models\Santri;
use App\Models\TambahanBulanan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SantriTambahanPembayaran>
 */
class SantriTambahanBulananFactory extends Factory
{
    public function definition(): array
    {
        return [
            'santri_id' => Santri::inRandomOrder()->first()->id_santri,
            'tambahan_bulanan_id' => TambahanBulanan::inRandomOrder()->first()->id_tambahan_bulanan,
            'jumlah' => $this->faker->numberBetween(1, 3),
        ];
    }
}
