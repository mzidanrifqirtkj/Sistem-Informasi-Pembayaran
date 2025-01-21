<?php

namespace Database\Factories;

use App\Models\BiayaTahunan;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TagihanBulanan;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tagihan>
 */
class TagihanBulananFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = TagihanBulanan::class;

    public function definition()
    {
        $santri = Santri::inRandomOrder()->first();

        if (!$santri) {
            throw new Exception('Santri tidak ditemukan. Harap tambahkan data ke tabel Santri.');
        }

        // Pilih secara acak apakah tagihan ini bulanan atau tahunan
        $nominal = $santri->kategori_santri->nominal_syahriyah;
        $bulan =  ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $randomMonth = $bulan[array_rand($bulan)];

        $rincian = [
            'nama_item' => 'syahriyah',
            'nominal' => $nominal,
            'bulan' => $randomMonth
        ];

        // Tambahan pembayaran
        foreach ($santri->tambahanPembayarans as $tambahan) {
            $rincian['tambahan'] = [
                'nama_item' => $tambahan->nama_item,
                'nominal' => $tambahan->nominal,
                'jumlah' => $tambahan->pivot->jumlah,
                'tahun' => now()->year,
            ];
            $nominal += $tambahan->pivot->jumlah * $tambahan->nominal;
        }

        return [
            'santri_id' => $santri->id_santri,
            'bulan' => $randomMonth,
            'tahun' => now()->year,
            'rincian' => $rincian,
            'nominal' => $nominal,
            'status' => 'belum_lunas',
        ];

    }

}
