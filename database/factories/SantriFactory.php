<?php

namespace Database\Factories;

use App\Models\KategoriSantri;
use App\Models\PaketPembayaran;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Santri>
 */
class SantriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Santri::class;

    public function definition(): array
    {
        return [
            'id_santri' => $this->faker->unique()->randomNumber(4),
            'nama_santri' => $this->faker->name,
            'nis' => $this->faker->unique()->numerify('####'),
            'nik' => $this->faker->unique()->numerify('##########'),
            'no_kk' => $this->faker->unique()->numerify('##########'),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tempat_lahir' => $this->faker->city,
            'tanggal_lahir' => $this->faker->date(),
            'no_hp' => $this->faker->phoneNumber,
            'alamat' => $this->faker->address,
            'golongan_darah' => $this->faker->randomElement(['A', 'B', 'AB', 'O']),
            'pendidikan_formal' => $this->faker->randomElement(['SD', 'SMP', 'SMA']),
            'pendidikan_non_formal' => $this->faker->randomElement(['Tahfidz', 'Pra Tahfidz']),
            'tanggal_masuk' => $this->faker->date(),
            'is_ustadz' => $this->faker->boolean,

            'user_id' => User::factory(),
            'kategori_santri_id' => KategoriSantri::inRandomOrder()->first()->id_kategori_santri,

            'nama_ayah' => $this->faker->name,
            'no_hp_ayah' => $this->faker->phoneNumber,
            'pekerjaan_ayah' => $this->faker->jobTitle,
            'tempat_lahir_ayah' => $this->faker->city,
            'tanggal_lahir_ayah' => $this->faker->date(),
            'alamat_ayah' => $this->faker->address,

            'nama_ibu' => $this->faker->name,
            'no_hp_ibu' => $this->faker->phoneNumber,
            'pekerjaan_ibu' => $this->faker->jobTitle,
            'alamat_ibu' => $this->faker->address,
            'tempat_lahir_ibu' => $this->faker->city,
            'tanggal_lahir_ibu' => $this->faker->date(),

            'nama_wali' => $this->faker->name,
            'no_hp_wali' => $this->faker->phoneNumber,
            'pekerjaan_wali' => $this->faker->jobTitle,
            'alamat_wali' => $this->faker->address,
            'tempat_lahir_wali' => $this->faker->city,
            'tanggal_lahir_wali' => $this->faker->date(),
            'status' => $this->faker->randomElement(['aktif', 'non_aktif']),
        ];
    }
}
