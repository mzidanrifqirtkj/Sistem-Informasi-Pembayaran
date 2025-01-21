<?php

namespace Database\Factories;

use App\Models\Santri;
use App\Models\TambahanPembayaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SantriTambahanPembayaran>
 */
class SantriTambahanPembayaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'santri_id' => Santri::inRandomOrder()->first()->id_santri,
            'tambahan_pembayaran_id' => TambahanPembayaran::inRandomOrder()->first()->id_tambahan_pembayaran,
            'jumlah' => $this->faker->numberBetween(1, 3),
        ];
    }
}
