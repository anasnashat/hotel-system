<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'receptionist']);
        Role::create(['name' => 'client']);

        // Create Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Create Dummy Managers
        $managers = [];
        for ($i = 1; $i <= 3; $i++) {
            $manager = User::create([
                'name' => "Manager $i",
                'email' => "manager$i@hotel.com",
                'password' => Hash::make('password'),
            ]);
            $manager->assignRole('manager');
            $managers[] = $manager;

            // Create Manager Profile
            UserProfile::create([
                'user_id' => $manager->id,
                'national_id' => "MGR000$i",
                'phone_number' => '1234567890',
                'country_code' => 'US',
                'created_by' => $admin->id,
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);
        }

        // Create Dummy Receptionists
        foreach ($managers as $manager) {
            for ($j = 1; $j <= 2; $j++) {
                $receptionist = User::create([
                    'name' => "Receptionist {$manager->id}-$j",
                    'email' => "receptionist{$manager->id}$j@hotel.com",
                    'password' => Hash::make('password'),
                ]);
                $receptionist->assignRole('receptionist');

                // Create Receptionist Profile
                UserProfile::create([
                    'user_id' => $receptionist->id,
                    'national_id' => "REC{$manager->id}$j",
                    'phone_number' => '9876543210',
                    'country_code' => 'UK',
                    'created_by' => $manager->id,
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                ]);
            }
        }

        // Create Dummy Clients
        for ($k = 1; $k <= 5; $k++) {
            $client = User::create([
                'name' => "Client $k",
                'email' => "client$k@hotel.com",
                'password' => Hash::make('password'),
            ]);
            $client->assignRole('client');

            // Create Client Profile
            UserProfile::create([
                'user_id' => $client->id,
                'national_id' => "CLI00$k",
                'phone_number' => '5555555555',
                'country_code' => 'FR',
                'created_by' => null, // Clients create themselves
                'approved_by' => null, // Pending approval
            ]);
        }
    }
}
