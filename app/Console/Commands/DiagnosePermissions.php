<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class DiagnosePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:permissions {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose permission issues in the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running permission diagnostics...');

        // 1. Check for package installation
        $this->info('1. Checking Spatie Permission Package...');
        $this->checkPackageInstallation();

        // 2. Check permissions cache
        $this->info('2. Checking Permission Cache...');
        $this->checkPermissionCache();

        // 3. Check roles and permissions in database
        $this->info('3. Checking Roles and Permissions in Database...');
        $this->checkRolesAndPermissions();

        // 4. Check specific user if provided
        $email = $this->argument('email') ?? 'admin@example.com';
        $this->info("4. Checking User: {$email}");
        $this->checkUser($email);

        // 5. Test permission assignment
        $this->info('5. Testing Permission Assignment...');
        $this->testPermissionAssignment($email);

        $this->info('Diagnostic complete. Check logs for detailed information.');
        return 0;
    }

    /**
     * Check if the package is properly installed
     */
    private function checkPackageInstallation()
    {
        try {
            $migrationStatus = $this->testMigrationStatus();
            if ($migrationStatus) {
                $this->info('✓ Permission tables exist in the database');
            } else {
                $this->error('✗ Permission tables may not exist in the database');
                $this->warn('Run: php artisan migrate to ensure all migrations are applied');
            }
        } catch (\Exception $e) {
            $this->error('✗ Error checking package installation: ' . $e->getMessage());
            Log::error('Permission diagnostic error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Check permission cache status
     */
    private function checkPermissionCache()
    {
        try {
            $this->info('Clearing permission cache...');
            Artisan::call('permission:cache-reset');
            $this->info('✓ Permission cache cleared successfully');
        } catch (\Exception $e) {
            $this->error('✗ Error clearing permission cache: ' . $e->getMessage());
            Log::error('Permission cache reset error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Check roles and permissions in the database
     */
    private function checkRolesAndPermissions()
    {
        try {
            $roleModel = app(config('permission.models.role'));
            $permissionModel = app(config('permission.models.permission'));

            $roles = $roleModel::all();
            $permissions = $permissionModel::all();

            if ($roles->isEmpty()) {
                $this->error('✗ No roles found in the database');
                $this->warn('Run: php artisan db:seed --class=RolesAndPermissionsSeeder');
            } else {
                $this->info('✓ Roles found: ' . $roles->pluck('name')->implode(', '));
            }

            if ($permissions->isEmpty()) {
                $this->error('✗ No permissions found in the database');
                $this->warn('Run: php artisan db:seed --class=RolesAndPermissionsSeeder');
            } else {
                $this->info('✓ Permissions found: ' . $permissions->count() . ' permissions');
                $this->line('Sample permissions: ' . $permissions->take(5)->pluck('name')->implode(', ') . '...');
            }

            // Check role-permission relationships
            foreach ($roles as $role) {
                $rolePermissions = $role->permissions()->pluck('name')->toArray();
                if (empty($rolePermissions)) {
                    $this->warn('! Role "' . $role->name . '" has no permissions');
                } else {
                    $this->info('✓ Role "' . $role->name . '" has ' . count($rolePermissions) . ' permissions');
                }
            }
        } catch (\Exception $e) {
            $this->error('✗ Error checking roles and permissions: ' . $e->getMessage());
            Log::error('Role/permission check error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Check a specific user's permissions
     */
    private function checkUser(string $email)
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("✗ User with email {$email} not found");
                return;
            }

            $this->info("User: {$user->name} ({$user->email})");

            // Check direct roles
            $roles = $user->roles()->pluck('name')->toArray();
            if (empty($roles)) {
                $this->warn("! User has no roles assigned");
            } else {
                $this->info("✓ User has roles: " . implode(', ', $roles));
            }

            // Check admin role specifically
            $hasAdminRole = $user->hasRole('admin');
            $checkMethod = $user->isAdmin();

            if ($hasAdminRole) {
                $this->info("✓ User has admin role (direct check)");
            } else {
                $this->error("✗ User does not have admin role (direct check)");
            }

            if ($checkMethod) {
                $this->info("✓ User is admin (using isAdmin() method)");
            } else {
                $this->error("✗ User is not admin (using isAdmin() method)");
            }

            // Check direct permissions
            $directPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
            if (empty($directPermissions)) {
                $this->line("User has no direct permissions");
            } else {
                $this->info("✓ User has direct permissions: " . implode(', ', $directPermissions));
            }

            // Check all permissions (including from roles)
            $allPermissions = $user->getAllPermissions()->pluck('name')->toArray();
            if (empty($allPermissions)) {
                $this->warn("! User has no permissions at all");
            } else {
                $this->info("✓ User has permissions (including from roles): " . implode(', ', $allPermissions));
            }
        } catch (\Exception $e) {
            $this->error('✗ Error checking user: ' . $e->getMessage());
            Log::error('User permission check error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Test permission assignment on the user
     */
    private function testPermissionAssignment(string $email)
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("✗ User with email {$email} not found");
                return;
            }

            // Test assigning the admin role
            $adminRole = Role::where('name', 'admin')->first();

            if (!$adminRole) {
                $this->error("✗ Admin role not found in database");
                return;
            }

            $this->info("Testing admin role assignment...");

            // Get current roles before assignment
            $beforeRoles = $user->roles()->pluck('name')->toArray();
            $this->line("Roles before: " . implode(', ', $beforeRoles ?: ['none']));

            // Force sync roles to include admin
            $user->syncRoles(['admin']);

            // Reload user from database
            $user = User::where('email', $email)->first();

            // Get roles after assignment
            $afterRoles = $user->roles()->pluck('name')->toArray();
            $this->line("Roles after: " . implode(', ', $afterRoles ?: ['none']));

            // Check if admin role was assigned
            if (in_array('admin', $afterRoles)) {
                $this->info("✓ Admin role successfully assigned to user");
            } else {
                $this->error("✗ Failed to assign admin role to user");
            }

            // Final check with hasRole method
            $hasAdminRole = $user->hasRole('admin');
            if ($hasAdminRole) {
                $this->info("✓ User has admin role (final check)");
            } else {
                $this->error("✗ User does not have admin role (final check)");
            }
        } catch (\Exception $e) {
            $this->error('✗ Error testing permission assignment: ' . $e->getMessage());
            Log::error('Permission assignment test error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Test if permission tables exist in the database
     */
    private function testMigrationStatus(): bool
    {
        try {
            $tableNames = config('permission.table_names');

            // Check if permission tables exist
            $tables = [
                $tableNames['roles'] ?? 'roles',
                $tableNames['permissions'] ?? 'permissions',
                $tableNames['model_has_permissions'] ?? 'model_has_permissions',
                $tableNames['model_has_roles'] ?? 'model_has_roles',
                $tableNames['role_has_permissions'] ?? 'role_has_permissions',
            ];

            $db = \DB::connection();
            $existingTables = 0;

            foreach ($tables as $table) {
                if ($db->getSchemaBuilder()->hasTable($table)) {
                    $existingTables++;
                } else {
                    $this->warn("Table {$table} does not exist");
                }
            }

            return $existingTables === count($tables);
        } catch (\Exception $e) {
            $this->error('Error checking migration status: ' . $e->getMessage());
            return false;
        }
    }
}
