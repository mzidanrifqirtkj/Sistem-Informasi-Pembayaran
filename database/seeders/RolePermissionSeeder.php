<?php

namespace Database\Seeders;

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
        Permission::create(['name' => 'add-user']);
        Permission::create(['name' => 'edit-user']);
        Permission::create(['name' => 'delete-user']);
        Permission::create(['name' => 'view-user']);

        Permission::create(['name' => 'add-absensi']);
        Permission::create(['name' => 'edit-absensi']);
        Permission::create(['name' => 'delete-absensi']);
        Permission::create(['name' => 'view-absensi']);

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'santri']);

        $roleAdmin = Role::findByName('admin');
        $roleAdmin->givePermissionTo('add-user');
        $roleAdmin->givePermissionTo('edit-user');
        $roleAdmin->givePermissionTo('delete-user');
        $roleAdmin->givePermissionTo('view-user');

        $roleAdmin = Role::findByName('santri');
        $roleAdmin->givePermissionTo('add-absensi');
        $roleAdmin->givePermissionTo('edit-absensi');
        $roleAdmin->givePermissionTo('delete-absensi');
        $roleAdmin->givePermissionTo('view-absensi');
    }
}
