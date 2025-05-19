<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Create a superadmin user using environment variables.
     */    public function run(): void
    {
        $superAdmin = User::create([
            'name' => env('SUPERADMIN_NAME', 'Super Admin'),
            'email' => env('SUPERADMIN_EMAIL', 'superadmin@example.com'),
            'password' => Hash::make(env('SUPERADMIN_PASSWORD', 'password')),
        ]);

        $superAdmin->assignRole('admin');

        $this->command->info('Superadmin user created successfully.');
    }
}
