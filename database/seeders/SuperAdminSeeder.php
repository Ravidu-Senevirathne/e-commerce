<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Create a superadmin user using environment variables.
     */
    public function run(): void
    {
        User::create([
            'name' => env('SUPERADMIN_NAME',),
            'email' => env('SUPERADMIN_EMAIL'),
            'password' => Hash::make(env('SUPERADMIN_PASSWORD')),
            'role_id' => 1, // Admin role
        ]);

        $this->command->info('Superadmin user created successfully.');
    }
}
