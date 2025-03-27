<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Santri;
use App\Models\User;
use App\Models\KategoriSantri;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil atau buat user dan kategori santri
        $user = User::firstOrCreate([
            // 'name' => 'Santri User',
            'email' => 'santri@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        $userZidan = User::firstOrCreate(
            ['email' => 'zidan@gmail.com'],
            ['password' => bcrypt('12345678')]
        );

        $kategoriSantri = KategoriSantri::firstOrCreate([
            'nama_kategori' => 'Santri Baru',
        ]);

        // Data santri contoh
        $santris = [
            [
                'nama_santri' => 'Santri A',
                'nis' => '201001',
                'nik' => '1234567890123456',
                'no_kk' => '9876543210987654',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '2010-01-01',
                'tempat_lahir' => 'Jakarta',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 1',
                'golongan_darah' => 'A',
                'pendidikan_formal' => 'SD',
                'pendidikan_non_formal' => 'TPQ',
                'foto' => null,
                'foto_kk' => null,
                'tanggal_masuk' => '2023-07-01',
                'is_ustadz' => false,
                'user_id' => $user->id_user,
                'kategori_santri_id' => $kategoriSantri->id_kategori_santri,
                'nama_ayah' => 'Ayah Santri A',
                'no_hp_ayah' => '081234567891',
                'pekerjaan_ayah' => 'Wiraswasta',
                'tempat_lahir_ayah' => 'Jakarta',
                'tanggal_lahir_ayah' => '1980-01-01',
                'alamat_ayah' => 'Jl. Merdeka No. 1',
                'nama_ibu' => 'Ibu Santri A',
                'no_hp_ibu' => '081234567892',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'alamat_ibu' => 'Jl. Merdeka No. 1',
                'tempat_lahir_ibu' => 'Jakarta',
                'tanggal_lahir_ibu' => '1985-01-01',
                'nama_wali' => null,
                'no_hp_wali' => null,
                'pekerjaan_wali' => null,
                'alamat_wali' => null,
                'tempat_lahir_wali' => null,
                'tanggal_lahir_wali' => null,
                'status' => 'Aktif',
                'tabungan' => 0,
            ],
            [
                'nama_santri' => 'Zidan',
                'nis' => '201002',
                'nik' => '2233445566778899',
                'no_kk' => '9988776655443322',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '2011-05-15',
                'tempat_lahir' => 'Bandung',
                'no_hp' => '081298765432',
                'alamat' => 'Jl. Cendana No. 7',
                'golongan_darah' => 'B',
                'pendidikan_formal' => 'SMP',
                'pendidikan_non_formal' => 'Ponpes',
                'foto' => null,
                'foto_kk' => null,
                'tanggal_masuk' => '2023-08-10',
                'is_ustadz' => false,
                'user_id' => $userZidan->id_user,
                'kategori_santri_id' => $kategoriSantri->id_kategori_santri,
                'nama_ayah' => 'Ayah Zidan',
                'no_hp_ayah' => '081212345678',
                'pekerjaan_ayah' => 'Pegawai Negeri',
                'tempat_lahir_ayah' => 'Bandung',
                'tanggal_lahir_ayah' => '1982-03-10',
                'alamat_ayah' => 'Jl. Cendana No. 7',
                'nama_ibu' => 'Ibu Zidan',
                'no_hp_ibu' => '081234567123',
                'pekerjaan_ibu' => 'Guru',
                'alamat_ibu' => 'Jl. Cendana No. 7',
                'tempat_lahir_ibu' => 'Bandung',
                'tanggal_lahir_ibu' => '1986-09-21',
                'status' => 'Aktif',
                'tabungan' => 50000,
            ],
        ];

        // Insert data ke tabel santris
        foreach ($santris as $santri) {
            Santri::create($santri);
        }

        $this->command->info('Data santri berhasil ditambahkan!');
    }
}
