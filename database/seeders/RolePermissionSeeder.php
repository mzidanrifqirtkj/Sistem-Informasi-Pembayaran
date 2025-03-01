<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data lama tanpa truncate
        Role::query()->delete();
        Permission::query()->delete();
        DB::table('model_has_roles')->delete(); // Hapus relasi agar tidak error

        // Reset auto-increment (opsional)
        DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1;');
        DB::statement('ALTER TABLE permissions AUTO_INCREMENT = 1;');

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Membuat role
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleSantri = Role::create(['name' => 'santri']);

        // Membuat permission untuk setiap fitur
        $adminPermissions = [
            // Dashboard
            'view_dashboard',

            // Profile
            'view_profile',

            // Santri
            'view_santri',
            'create_santri',
            'edit_santri',
            'delete_santri',
            'import_santri',

            // User
            'view_user',
            'create_user',
            'edit_user',
            'delete_user',
            'import_user',

            // Kategori Santri
            'view_kategori_santri',
            'create_kategori_santri',
            'edit_kategori_santri',
            'delete_kategori_santri',

            // Tagihan Terjadwal
            'view_tagihan_terjadwal',
            'create_tagihan_terjadwal',
            'edit_tagihan_terjadwal',
            'delete_tagihan_terjadwal',
            'bulk_generate_tagihan_terjadwal',

            // Tambahan Bulanan
            'view_tambahan_bulanan',
            'create_tambahan_bulanan',
            'edit_tambahan_bulanan',
            'delete_tambahan_bulanan',
            'view_item_santri',
            'edit_item_santri',

            // Tagihan Bulanan
            'view_tagihan_bulanan',
            'create_tagihan_bulanan',
            'edit_tagihan_bulanan',
            'delete_tagihan_bulanan',
            'bulk_generate_tagihan_bulanan',

            // Pembayaran
            'view_pembayaran',
            'create_pembayaran',
            'edit_pembayaran',
            'delete_pembayaran',
            'view_riwayat_pembayaran',

            // Biaya Terjadwal
            'view_biaya_terjadwal',
            'create_biaya_terjadwal',
            'edit_biaya_terjadwal',
            'delete_biaya_terjadwal',

            // Kelas
            'view_kelas',
            'create_kelas',
            'edit_kelas',
            'delete_kelas',

            // Mapel Kelas
            'view_mapel_kelas',
            'create_mapel_kelas',
            'edit_mapel_kelas',
            'delete_mapel_kelas',

            // Tahun Ajar
            'view_tahun_ajar',
            'create_tahun_ajar',
            'edit_tahun_ajar',
            'delete_tahun_ajar',

            // Mata Pelajaran
            'view_mapel',
            'create_mapel',
            'edit_mapel',
            'delete_mapel',

            // Ustadz
            'view_ustadz',
            'create_ustadz',
            'edit_ustadz',
            'delete_ustadz',

            // Penugasan Ustadz
            'view_penugasan_ustadz',
            'create_penugasan_ustadz',
            'edit_penugasan_ustadz',
            'delete_penugasan_ustadz',
            'get_wali_kelas',
            'get_qori',
            'create_qori',
            'get_pelajaran',
            'store_qori',
            'create_mustahiq',
            'get_kelas',
            'store_mustahiq',

            // Absensi
            'view_absensi',
            'create_absensi',
            'edit_absensi',
            'delete_absensi',
            'import_absensi',
            'get_santri_list',

            // Profile
            'view_profile',
            'edit_profile',
        ];

        // Memberikan permission terbatas ke role santri
        $santriPermissions = [
            'view_dashboard', // Santri bisa melihat dashboard
            'view_santri',   // Santri bisa melihat data santri (mungkin hanya data dirinya sendiri)
            'view_absensi',  // Santri bisa melihat absensi
            'view_item_santri',
            'view_tagihan_terjadwal',
            'view_tagihan_bulanan',
            'view_riwayat_pembayaran',
            'view_profile',
            'edit_profile',
        ];

        // Menambahkan permission ke database
        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($santriPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Memberikan permission ke role admin
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->syncPermissions($adminPermissions);

        // Memberikan permission ke role santri
        $roleSantri = Role::firstOrCreate(['name' => 'santri']);
        $roleSantri->syncPermissions($santriPermissions);
    }
}
