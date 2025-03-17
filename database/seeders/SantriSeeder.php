<?php

namespace Database\Seeders;

use App\Models\Santri;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Santri::create([
            'nama_santri' => 'Ahmad Ubaidillah',
            'nis' => '2025001',
            'nik' => '3210010101010001',
            'no_kk' => '3210010101010001',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2001-04-13',
            'tempat_lahir' => 'Cirebon',
            'no_hp' => '081234567890',
            'alamat' => 'Yogyakarta',
            'golongan_darah' => 'O',
            'pendidikan_formal' => 'S1',
            'pendidikan_non_formal' => 'Tahfidz',
            'foto' => null,
            'foto_kk' => null,
            'tanggal_masuk' => '2025-01-01',
            'is_ustadz' => false,
            'user_id' => null, // Sesuaikan nanti setelah user dibuat
            'kategori_santri_id' => 1,
            'nama_ayah' => 'Bapak Ahmad',
            'no_hp_ayah' => '081234567891',
            'pekerjaan_ayah' => 'Petani',
            'tempat_lahir_ayah' => 'Cirebon',
            'tanggal_lahir_ayah' => '1975-05-10',
            'alamat_ayah' => 'Cirebon',
            'nama_ibu' => 'Ibu Ahmad',
            'no_hp_ibu' => '081234567892',
            'pekerjaan_ibu' => 'Ibu Rumah Tangga',
            'alamat_ibu' => 'Cirebon',
            'tempat_lahir_ibu' => 'Cirebon',
            'tanggal_lahir_ibu' => '1980-08-15',
            'nama_wali' => 'Pak Wali',
            'no_hp_wali' => '081234567893',
            'pekerjaan_wali' => 'Guru',
            'alamat_wali' => 'Yogyakarta',
            'tempat_lahir_wali' => 'Cirebon',
            'tanggal_lahir_wali' => '1970-03-20',
            'status' => 'Aktif',
            'tabungan' => 500000,
        ]);
    }
}
