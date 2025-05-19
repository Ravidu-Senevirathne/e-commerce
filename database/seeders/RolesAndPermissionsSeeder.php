<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for products
        $productPermissions = [
            'view products',
            'create products',
            'edit products',
            'delete products',
        ];

        // Create permissions for users
        $userPermissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
        ];

        // Create all permissions
        $allPermissions = array_merge($productPermissions, $userPermissions);
        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create admin role and give it all permissions
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrator with full access'
        ]);
        $adminRole->givePermissionTo($allPermissions);

        // Create user role with limited permissions
        $userRole = Role::create([
            'name' => 'user',
            'description' => 'Regular user with limited access'
        ]);
        $userRole->givePermissionTo('view products');

        // Optionally create a super-admin
        // Create a super-admin user if needed
        $user = \App\Models\User::where('email', env('SUPERADMIN_EMAIL', 'superadmin@example.com'))->first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
