<?php

namespace App\Imports;

use App\Models\BiayaSantri;
use App\Models\DaftarBiaya;
use App\Models\KategoriBiaya;
use App\Models\Santri;
use App\Models\User;
use DateTime;
use DateTimeZone;
use DB;
use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SantriImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        DB::beginTransaction();

        try {
            $timeZone = timezone_open('Asia/Jakarta') ?: new DateTimeZone('UTC');

            // Fungsi untuk memproses tanggal, jika kosong maka null
            $parseDate = function ($dateString) use ($timeZone) {
                if (empty($dateString)) {
                    return null;
                }
                try {
                    return new DateTime($dateString, $timeZone);
                } catch (Exception $e) {
                    return null;
                }
            };

            $tanggalLahir = $parseDate($row['tanggal_lahir']);
            $tanggalMasuk = $parseDate($row['tanggal_masuk']);
            $tanggalKeluar = $parseDate($row['tanggal_keluar']);
            $tanggalLahirAyah = $parseDate($row['tanggal_lahir_ayah']);
            $tanggalLahirIbu = $parseDate($row['tanggal_lahir_ibu']);

            $email = !empty($row['email']) ? $row['email'] : null;
            $password = bcrypt($row['nis'] . '123');

            if (!empty($row['email']) && filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                $email = $row['email'];
            } else {
                $email = null;
            }

            $user = new User([
                'email' => $email,
                'password' => $password,
            ]);
            $user->save();
            $user->assignRole('santri');

            $isUstadz = !empty($row['is_ustadz']) ? (bool) $row['is_ustadz'] : false;

            // Mapping jenis kelamin ke format L/P
            $jenisKelaminMapping = [
                'Laki-laki' => 'L',
                'Perempuan' => 'P',
                'laki-laki' => 'L', // case insensitive
                'perempuan' => 'P',
                'LAKI-LAKI' => 'L',
                'PEREMPUAN' => 'P',
                // Fallback jika sudah format L/P
                'L' => 'L',
                'P' => 'P'
            ];
            // Jika jenis kelamin tidak ada atau tidak dikenali, akan tetap null
            // Jika jenis kelamin sudah dalam format L/P, akan tetap L/P
            // Jika jenis kelamin tidak ada, akan tetap null
            // Jika jenis kelamin tidak dikenali, akan tetap null
            // Jika jenis kelamin sudah dalam format L/P, akan tetap L/P
            // Jika jenis kelamin tidak ada, akan tetap null
            // Jika jenis kelamin tidak dikenali, akan tetap null
            // Jika jenis kelamin sudah dalam format L/P, akan tetap L/P
            $jenisKelamin = null;
            if (!empty($row['jenis_kelamin'])) {
                $jenisKelamin = $jenisKelaminMapping[$row['jenis_kelamin']] ?? null;
            }

            $santri = Santri::create([
                'nama_santri' => $row['nama_lengkap'] ?? null,
                'nis' => !empty($row['nis']) ? (int) $row['nis'] : null,
                'nik' => $row['nik'] ?? null,
                'no_kk' => $row['no_kk'] ?? null,

                'jenis_kelamin' => $jenisKelamin,
                'tanggal_lahir' => $tanggalLahir ? $tanggalLahir->format('Y-m-d') : null,
                'tempat_lahir' => $row['tempat_lahir'] ?? null,
                'no_hp' => $row['no_telepon'] ?? null,
                'alamat' => $row['alamat'] ?? null,
                'golongan_darah' => $row['golongan_darah'] ?? null,
                'pendidikan_formal' => $row['pendidikan_formal_terakhir'] ?? null,
                'pendidikan_non_formal' => $row['pendidikan_nonformal_terakhir'] ?? null,
                'tanggal_masuk' => $tanggalMasuk ? $tanggalMasuk->format('Y-m-d') : null,
                'tanggal_keluar' => $tanggalKeluar ? $tanggalKeluar->format('Y-m-d') : null,
                'is_ustadz' => $isUstadz,
                'user_id' => $user->id_user,
                'nama_ayah' => $row['nama_ayah'] ?? null,
                'no_hp_ayah' => $row['no_telepon_ayah'] ?? null,
                'pekerjaan_ayah' => $row['pekerjaan_ayah'] ?? null,
                'tempat_lahir_ayah' => $row['tempat_lahir_ayah'] ?? null,
                'tanggal_lahir_ayah' => $tanggalLahirAyah ? $tanggalLahirAyah->format('Y-m-d') : null,
                'alamat_ayah' => $row['alamat_ayah'] ?? null,
                'nama_ibu' => $row['nama_ibu'] ?? null,
                'no_hp_ibu' => $row['no_telepon_ibu'] ?? null,
                'pekerjaan_ibu' => $row['pekerjaan_ibu'] ?? null,
                'alamat_ibu' => $row['alamat_ibu'] ?? null,
                'tempat_lahir_ibu' => $row['tempat_lahir_ibu'] ?? null,
                'tanggal_lahir_ibu' => $tanggalLahirIbu ? $tanggalLahirIbu->format('Y-m-d') : null,
                'status' => $row['status_santri'] ?? null,
            ]);

            $defaultKategoriBiaya = KategoriBiaya::where('nama_kategori', 'Reguler')
                ->where('status', 'jalur')->first();

            $defaultDaftarBiaya = DaftarBiaya::where('kategori_biaya_id', $defaultKategoriBiaya->id_kategori_biaya)->first();

            BiayaSantri::create([
                'santri_id' => $santri->id_santri,
                'daftar_biaya_id' => $defaultDaftarBiaya->id_daftar_biaya,
                'jumlah' => 1 // atau sesuai kebutuhan
            ]);

            DB::commit();
            return $santri;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
