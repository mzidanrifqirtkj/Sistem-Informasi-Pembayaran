<?php

namespace App\Imports;

use App\Models\Santri;
use DateTime;
use DateTimeZone;
use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SantriImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $timeZone = timezone_open('Asia/Jakarta') ?: new DateTimeZone('UTC');

        // Fungsi untuk memvalidasi dan memproses tanggal
        $parseDate = function ($dateString, $columnName) use ($timeZone) {
            if (empty($dateString)) {
                throw new Exception("Kolom '{$columnName}' tidak boleh kosong.");
            }

            try {
                return new DateTime($dateString, $timeZone);
            } catch (Exception $e) {
                throw new Exception("Format tanggal pada kolom '{$columnName}' tidak valid: {$dateString}");
            }
        };

        // Parsing semua tanggal
        $tanggalLahir = $parseDate($row['tanggal_lahir'], 'tanggal_lahir');
        $tanggalMasuk = $parseDate($row['tanggal_masuk'], 'tanggal_masuk');
        $tanggalLahirAyah = $parseDate($row['tanggal_lahir_ayah'], 'tanggal_lahir_ayah');
        $tanggalLahirIbu = $parseDate($row['tanggal_lahir_ibu'], 'tanggal_lahir_ibu');

        return new Santri([
            'nama_santri'           => $row['nama_santri'],
            'nis'                   => (int)$row['nis'],
            'nik'                   => $row['nik'],
            'no_kk'                 => $row['no_kk'],
            'jenis_kelamin'         => $row['jenis_kelamin'],
            'tanggal_lahir'         => $tanggalLahir->format('Y-m-d'),
            'tempat_lahir'          => $row['tempat_lahir'],
            'no_hp'                 => $row['no_hp'],
            'alamat'                => $row['alamat'],
            'golongan_darah'        => $row['golongan_darah'],
            'pendidikan_formal'     => $row['pendidikan_formal'],
            'pendidikan_non_formal' => $row['pendidikan_non_formal'],
            // 'foto'                  => $row['foto'],
            // 'foto_kk'               => $row['foto_kk'],
            'tanggal_masuk'         => $tanggalMasuk->format('Y-m-d'),
            'is_ustadz'             => $row['is_ustadz'],
            'user_id'               => $row['user_id'],
            'kategori_santri_id'    => $row['kategori_santri_id'],
            'nama_ayah'             => $row['nama_ayah'],
            'no_hp_ayah'            => $row['no_hp_ayah'],
            'pekerjaan_ayah'        => $row['pekerjaan_ayah'],
            'tempat_lahir_ayah'     => $row['tempat_lahir_ayah'],
            'tanggal_lahir_ayah'    => $tanggalLahirAyah->format('Y-m-d'),
            'alamat_ayah'           => $row['alamat_ayah'],
            'nama_ibu'              => $row['nama_ibu'],
            'no_hp_ibu'             => $row['no_hp_ibu'],
            'pekerjaan_ibu'         => $row['pekerjaan_ibu'],
            'alamat_ibu'            => $row['alamat_ibu'],
            'tempat_lahir_ibu'      => $row['tempat_lahir_ibu'],
            'tanggal_lahir_ibu'     => $tanggalLahirIbu->format('Y-m-d'),
            'status'                => $row['status'],
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_santri'           => 'required|string|max:255',
            'nis'                   => 'required|numeric|unique:santris,nis',
            'nik'                   => 'required|string|unique:santris,nik',
            'no_kk'                 => 'required|string|max:16',
            'jenis_kelamin'         => 'required|string|in:Laki-laki,Perempuan',
            'tanggal_lahir'         => 'required|date',
            'tempat_lahir'          => 'required|string|max:255',
            'no_hp'                 => 'required|string',
            'alamat'                => 'required|string|max:500',
            'golongan_darah'        => 'required|string|in:A,B,AB,O',
            'pendidikan_formal'     => 'required|string|max:255',
            'pendidikan_non_formal' => 'required|string|max:255',
            // 'foto'                  => 'nullable|string|max:500',
            // 'foto_kk'               => 'nullable|string|max:500',
            'tanggal_masuk'         => 'required|date',
            'is_ustadz'             => 'required|boolean',
            'user_id'               => 'required|exists:users,id_user',
            'kategori_santri_id'    => 'required|exists:kategori_santris,id_kategori_santri',
            'nama_ayah'             => 'required|string|max:255',
            'no_hp_ayah'            => 'required|numeric',
            'pekerjaan_ayah'        => 'required|string|max:255',
            'tempat_lahir_ayah'     => 'required|string|max:255',
            'tanggal_lahir_ayah'    => 'required|date',
            'alamat_ayah'           => 'required|string|max:500',
            'nama_ibu'              => 'required|string|max:255',
            'no_hp_ibu'             => 'required|numeric',
            'pekerjaan_ibu'         => 'required|string|max:255',
            'alamat_ibu'            => 'required|string|max:500',
            'tempat_lahir_ibu'      => 'required|string|max:255',
            'tanggal_lahir_ibu'     => 'required|date',
            'status'                => 'required|string|in:Aktif,Tidak Aktif',
        ];
    }
}
