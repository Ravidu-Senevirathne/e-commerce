<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class FixAdminRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:admin-roles {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix admin role assignments and permission issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting admin roles fix process...');

        // 1. Clear all caches
        $this->info('1. Clearing all caches...');
        $this->clearAllCaches();

        // 2. Reset permission cache
        $this->info('2. Resetting permission cache...');
        try {
            Artisan::call('permission:cache-reset');
            $this->info('✓ Permission cache reset successful');
        } catch (\Exception $e) {
            $this->warn('! Unable to reset permission cache: ' . $e->getMessage());
        }

        // 3. Fix database issues
        $this->info('3. Checking database for issues...');
        $this->fixDatabaseIssues();

        // 4. Recreate admin role if needed
        $this->info('4. Verifying admin role exists...');
        $this->recreateAdminRoleIfNeeded();

        // 5. Fix admin user
        $email = $this->argument('email') ?? 'admin@example.com';
        $this->info("5. Fixing admin user: {$email}");
        $this->fixAdminUser($email);

        $this->info('Admin role fix process completed.');

        return 0;
    }

    /**
     * Clear all Laravel caches
     */
    private function clearAllCaches(): void
    {
        $cacheCommands = [
            'cache:clear',
            'config:clear',
            'route:clear',
            'view:clear',
            'clear-compiled',
        ];

        foreach ($cacheCommands as $command) {
            try {
                Artisan::call($command);
                $this->line("✓ {$command} executed successfully");
            } catch (\Exception $e) {
                $this->warn("! Error executing {$command}: " . $e->getMessage());
            }
        }
    }

    /**
     * Fix database issues related to permissions
     */
    private function fixDatabaseIssues(): void
    {
        // Check and fix missing pivot tables
        $tableNames = config('permission.table_names');

        $requiredTables = [
            $tableNames['roles'] ?? 'roles',
            $tableNames['permissions'] ?? 'permissions',
            $tableNames['model_has_permissions'] ?? 'model_has_permissions',
            $tableNames['model_has_roles'] ?? 'model_has_roles',
            $tableNames['role_has_permissions'] ?? 'role_has_permissions',
        ];

        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $this->error("! Table {$table} is missing. You may need to run migrations.");
            } else {
                $this->line("✓ Table {$table} exists");

                // Check if there are records in the table
                $count = DB::table($table)->count();
                $this->line("  - {$table} has {$count} records");
            }
        }
    }

    /**
     * Recreate the admin role if needed
     */
    private function recreateAdminRoleIfNeeded(): void
    {
        try {
            $adminRole = Role::where('name', 'admin')->first();

            if (!$adminRole) {
                $this->warn('! Admin role not found, creating it...');
                $adminRole = Role::create(['name' => 'admin']);
                $this->info('✓ Admin role created successfully');

                // Add permissions to admin role
                Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder', '--force' => true]);
                $this->info('✓ Seeded permissions for admin role');
            } else {
                $this->info('✓ Admin role exists with ID: ' . $adminRole->id);

                // Check if admin role has permissions
                $permissionCount = $adminRole->permissions()->count();
                if ($permissionCount === 0) {
                    $this->warn('! Admin role has no permissions, reseeding...');
                    Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder', '--force' => true]);
                    $this->info('✓ Seeded permissions for admin role');
                } else {
                    $this->info("✓ Admin role has {$permissionCount} permissions");
                }
            }
        } catch (\Exception $e) {
            $this->error('! Error recreating admin role: ' . $e->getMessage());
        }
    }

    /**
     * Fix admin user permissions
     */
    private function fixAdminUser(string $email): void
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("! User with email {$email} not found");
                return;
            }

            $this->info("Found user: {$user->name} ({$user->email})");

            // Check current roles
            $roles = $user->roles()->pluck('name')->toArray();
            $this->line('Current roles: ' . (empty($roles) ? 'none' : implode(', ', $roles)));

            // Clear and reassign admin role
            DB::table(config('permission.table_names.model_has_roles'))
                ->where('model_id', $user->getKey())
                ->where('model_type', get_class($user))
                ->delete();

            $this->line('Cleared existing role assignments');

            // Get admin role
            $adminRole = Role::where('name', 'admin')->first();

            if (!$adminRole) {
                $this->error('! Admin role not found, cannot assign to user');
                return;
            }

            // Assign admin role directly through the database
            DB::table(config('permission.table_names.model_has_roles'))->insert([
                'role_id' => $adminRole->id,
                'model_type' => get_class($user),
                'model_id' => $user->getKey(),
            ]);

            $this->info('✓ Admin role assigned directly through database');

            // Force refresh the user instance
            $user = User::where('email', $email)->first();

            // Final check
            $finalRoles = $user->roles()->pluck('name')->toArray();
            $this->line('Final roles: ' . (empty($finalRoles) ? 'none' : implode(', ', $finalRoles)));

            if (in_array('admin', $finalRoles)) {
                $this->info('✓ Admin role successfully assigned');
            } else {
                $this->error('! Failed to assign admin role');
            }
        } catch (\Exception $e) {
            $this->error('! Error fixing admin user: ' . $e->getMessage());
        }
    }
}
