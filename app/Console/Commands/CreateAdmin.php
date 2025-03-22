<?php

namespace App\Console\Commands;

use App\Models\User; // Adjust the namespace according to your User model
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin
                            {--name= : The email of the admin}
                            {--password= : The password of the admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve options
        $email = $this->option('name');
        $password = $this->option('password');

        // Validate inputs
        if (!$email || !$password) {
            $this->error('Both --name and --password options are required.');
            return;
        }

        // Check if the user already exists
        if (User::where('email', $email)->exists()) {
            $this->error('A user with this email already exists.');
            return;
        }

        // Create the admin user
        $user = User::create([
            'name' => 'Admin', // You can customize this or add another option for the name
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $user->assignRole('admin');


        $this->info('Admin user created successfully!');
    }
}
