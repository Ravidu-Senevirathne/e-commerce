<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CheckAdminRole extends Command
{
    protected $signature = 'check:admin-role {email? : The email of the user to check}';
    protected $description = 'Check if admin role exists and is assigned to users';

    public function handle()
    {
        $this->info('Checking admin role configuration...');

        // Check if admin role exists
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->error('The admin role does not exist! Run php artisan db:seed --class=RolesAndPermissionsSeeder');
            return 1;
        }

        $this->info('✓ Admin role exists with ID: ' . $adminRole->id);

        // Check users with admin role
        $adminUsers = User::role('admin')->get();

        if ($adminUsers->isEmpty()) {
            $this->error('No users have the admin role assigned!');
            return 1;
        }

        $this->info('Found ' . $adminUsers->count() . ' users with admin role:');

        foreach ($adminUsers as $user) {
            $this->line(" - {$user->name} ({$user->email})");
        }

        // Check specific user if email provided
        $email = $this->argument('email');
        if ($email) {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("User with email {$email} not found!");
                return 1;
            }

            $this->info("User {$user->name} has roles: " . implode(', ', $user->getRoleNames()->toArray()));
            $this->info("isAdmin() method returns: " . ($user->isAdmin() ? 'true' : 'false'));
            $this->info("hasRole('admin') returns: " . ($user->hasRole('admin') ? 'true' : 'false'));
        }

        return 0;
    }
}
