<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'receptionist']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
        $admin?->assignRole('admin');
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@manager.com',
            'password' => bcrypt('password'),
            'manager_id' => $admin->id,
        ]);
        $manager?->assignRole('manager');

        $receptionist = User::create([
            'name' => 'Receptionist',
            'email' => 'receptionist@receptionist.com',
            'password' => bcrypt('password'),
            'manager_id' => $manager->id,
        ]);
        $receptionist?->assignRole('receptionist');


    }
}
