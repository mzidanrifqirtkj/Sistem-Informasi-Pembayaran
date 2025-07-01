<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset database
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::query()->delete();
        Permission::query()->delete();
        DB::table('model_has_roles')->delete();
        DB::table('model_has_permissions')->delete();
        DB::table('role_has_permissions')->delete();
        DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1;');
        DB::statement('ALTER TABLE permissions AUTO_INCREMENT = 1;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // All permissions dengan Laravel standard format
        $permissions = [
            // Dashboard & Profile
            'dashboard.view',
            'profile.view',
            'profile.edit',

            // Santri Management
            'santri.view',
            'santri.create',
            'santri.edit',
            'santri.delete',
            'santri.import',

            // User Management
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.import',

            // Roles & Permissions Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Financial - Biaya
            'biaya-santri.view',
            'biaya-santri.create',
            'biaya-santri.edit',
            'biaya-santri.delete',
            'daftar-biaya.view',
            'daftar-biaya.create',
            'daftar-biaya.edit',
            'daftar-biaya.delete',
            'kategori-biaya.view',
            'kategori-biaya.create',
            'kategori-biaya.edit',
            'kategori-biaya.delete',

            // Financial - Tagihan
            'tagihan-terjadwal.view',
            'tagihan-terjadwal.create',
            'tagihan-terjadwal.edit',
            'tagihan-terjadwal.delete',
            'tagihan-terjadwal.export',
            'tagihan-bulanan.view',
            'tagihan-bulanan.create',
            'tagihan-bulanan.edit',
            'tagihan-bulanan.delete',
            'tagihan-bulanan.export',
            'tambahan-bulanan.view',
            'tambahan-bulanan.create',
            'tambahan-bulanan.edit',
            'tambahan-bulanan.delete',
            'item-santri.view',
            'item-santri.edit',

            // Financial - Pembayaran
            'pembayaran.list',
            'pembayaran.create',
            'pembayaran.view',
            'pembayaran.void',
            'pembayaran.bulk',
            'pembayaran.history',

            // Academic - Kurikulum
            'kelas.view',
            'kelas.create',
            'kelas.edit',
            'kelas.delete',
            'mapel.view',
            'mapel.create',
            'mapel.edit',
            'mapel.delete',
            'mapel-kelas.view',
            'mapel-kelas.create',
            'mapel-kelas.edit',
            'mapel-kelas.delete',
            'tahun-ajar.view',
            'tahun-ajar.create',
            'tahun-ajar.edit',
            'tahun-ajar.delete',
            'qori-kelas.view',
            'qori-kelas.create',
            'qori-kelas.edit',
            'qori-kelas.delete',
            'riwayat-kelas.view',
            'riwayat-kelas.create',
            'riwayat-kelas.edit',
            'riwayat-kelas.delete',

            // Academic - Ustadz Management
            'ustadz.view',
            'ustadz.create',
            'ustadz.edit',
            'ustadz.delete',
            'penugasan-ustadz.view',
            'penugasan-ustadz.create',
            'penugasan-ustadz.edit',
            'penugasan-ustadz.delete',

            // Future: Absensi & Penilaian (template for later)
            // 'absensi.view',
            // 'absensi.create',
            // 'absensi.edit',
            // 'absensi.delete',
            // 'penilaian.view',
            // 'penilaian.create',
            // 'penilaian.edit',
            // 'penilaian.delete',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $santriRole = Role::create(['name' => 'santri']);
        $ustadzRole = Role::create(['name' => 'ustadz']);

        // ADMIN: Full access to everything
        $adminPermissions = $permissions; // All permissions
        $adminRole->givePermissionTo($adminPermissions);

        // SANTRI: Limited view permissions (own data only via policy)
        $santriPermissions = [
            // Basic access
            'dashboard.view',
            'profile.view',
            'profile.edit',

            // View own santri data (policy will restrict to own data)
            'santri.view',

            // View financial data (policy will restrict to own data)
            'biaya-santri.view',
            'daftar-biaya.view',
            'kategori-biaya.view',
            'tagihan-terjadwal.view',
            'tagihan-bulanan.view',
            'tambahan-bulanan.view',
            'item-santri.view',
            'pembayaran.list',
            'pembayaran.view',
            'pembayaran.history',

            // View academic data (policy will restrict to own data)
            'kelas.view',
            'mapel.view',
            'mapel-kelas.view',
            'tahun-ajar.view',
            'qori-kelas.view',
            'riwayat-kelas.view',

            // Future: Own academic data
            // 'absensi.view',
            // 'penilaian.view',
        ];
        $santriRole->givePermissionTo($santriPermissions);

        // USTADZ: Academic permissions + own data + limited student academic data
        $ustadzPermissions = [
            // Basic access (same as santri)
            'dashboard.view',
            'profile.view',
            'profile.edit',

            // View own santri data
            'santri.view',

            // View financial data (own data only via policy)
            'biaya-santri.view',
            'daftar-biaya.view',
            'kategori-biaya.view',
            'tagihan-terjadwal.view',
            'tagihan-bulanan.view',
            'tambahan-bulanan.view',
            'item-santri.view',
            'pembayaran.list',
            'pembayaran.view',
            'pembayaran.history',

            // Academic permissions (full access to manage classes)
            'kelas.view',
            'mapel.view',
            'mapel-kelas.view',
            'tahun-ajar.view',
            'qori-kelas.view',
            'riwayat-kelas.view', // Can view student class history in taught classes

            // Ustadz management (view only)
            'ustadz.view',
            'penugasan-ustadz.view',

            // Future: Academic data for students in taught classes
            // 'absensi.view',
            // 'absensi.create',
            // 'absensi.edit',
            // 'absensi.delete',
            // 'penilaian.view',
            // 'penilaian.create',
            // 'penilaian.edit',
            // 'penilaian.delete',
        ];
        $ustadzRole->givePermissionTo($ustadzPermissions);

        echo "✅ Roles and Permissions created successfully!\n";
        echo "📊 Admin: " . count($adminPermissions) . " permissions\n";
        echo "👨‍🎓 Santri: " . count($santriPermissions) . " permissions\n";
        echo "👨‍🏫 Ustadz: " . count($ustadzPermissions) . " permissions\n";
    }
}
