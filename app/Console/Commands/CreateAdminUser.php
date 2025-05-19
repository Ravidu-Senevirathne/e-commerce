<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'create:admin-user {email} {name} {password}';
    protected $description = 'Create a new admin user';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $password = $this->argument('password');

        // Check if user exists
        $user = User::where('email', $email)->first();

        if ($user) {
            $this->info("User {$email} already exists. Assigning admin role...");
        } else {
            // Create new user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $this->info("Created new user: {$email}");
        }

        // Assign admin role
        $user->assignRole('admin');

        $this->info("Successfully assigned admin role to {$email}");

        return 0;
    }
}
