<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CheckUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:check-role {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check which roles a user has';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found");
            return 1;
        }

        $this->info("User: {$user->name} ({$user->email})");

        $roles = $user->roles()->pluck('name')->toArray();

        if (empty($roles)) {
            $this->warn("User has no roles assigned");
        } else {
            $this->info("User has the following roles:");
            foreach ($roles as $role) {
                $this->line(" - {$role}");
            }
        }

        $this->info("Admin check: " . ($user->hasRole('admin') ? 'YES' : 'NO'));

        // Show all available roles in the system
        $this->info("\nAvailable roles in the system:");
        $allRoles = Role::all();
        foreach ($allRoles as $role) {
            $this->line(" - {$role->name}");
        }

        return 0;
    }
}
