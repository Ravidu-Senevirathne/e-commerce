<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SeedAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:admin {email?} {name?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@example.com';
        $name = $this->argument('name') ?? 'Admin User';
        $password = $this->argument('password') ?? 'password';

        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Check if user exists
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            $this->info("Created new admin user: {$email}");
        } else {
            $this->info("Admin user {$email} already exists");
        }

        // Assign admin role
        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
            $this->info("Successfully assigned admin role to {$email}");
        } else {
            $this->info("User {$email} already has admin role");
        }

        $this->info("Admin user setup complete");
    }
}
