<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Santri;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Data admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Cek berdasarkan email
            [
                'nis' => null, // Admin tidak memiliki NIS
                'password' => Hash::make('12345678'), // Password: 12345678
                'email_verified_at' => now(),
            ]
        );

        // Berikan role admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        // Data santri
        // $santri = Santri::first(); // Ambil santri pertama yang sudah dibuat
        // if ($santri) {
        //     $santriUser = User::firstOrCreate(
        //         ['email' => 'santri@example.com'], // Cek berdasarkan email
        //         [
        //             'nis' => $santri->nis, // Gunakan NIS dari santri
        //             'password' => Hash::make('12345678'), // Password: 12345678
        //             'email_verified_at' => now(),
        //         ]
        //     );

        //     // Berikan role santri
        //     $santriRole = Role::where('name', 'santri')->first();
        //     if ($santriRole) {
        //         $santriUser->assignRole($santriRole);
        //     }
        // }

        $this->command->info('Data admin ditambahkan!');
    }
}
