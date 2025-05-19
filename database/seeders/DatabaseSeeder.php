<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the role seeder first
        $this->call(RoleSeeder::class);

        // Create superadmin from .env
        $this->call(SuperAdminSeeder::class);

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => 1, // Admin role
        ]);

        // Create regular user
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // Regular user role
        ]);

        // Create some sample products
        $this->createSampleProducts();

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
    }

    /**
     * Create sample products for the store
     */
    private function createSampleProducts(): void
    {
        $products = [
            [
                'name' => 'Smartphone X',
                'description' => 'Latest model with 5G capabilities, 128GB storage and stunning camera.',
                'price' => 799.99,
                'image' => 'products/smartphone.jpg',
                'featured' => true
            ],
            [
                'name' => 'Wireless Headphones',
                'description' => 'Noise-cancelling headphones with 40 hours of battery life.',
                'price' => 199.99,
                'image' => 'products/headphones.jpg',
                'featured' => true
            ],
            [
                'name' => 'Fitness Tracker',
                'description' => 'Track your steps, heart rate, and sleep patterns with this waterproof tracker.',
                'price' => 89.99,
                'image' => 'products/tracker.jpg',
                'featured' => true
            ],
            [
                'name' => 'Smart Watch',
                'description' => 'Stay connected with notifications and track your fitness goals.',
                'price' => 249.99,
                'image' => 'products/watch.jpg',
                'featured' => false
            ],
            [
                'name' => 'Laptop Pro',
                'description' => 'Powerful laptop for professionals with 16GB RAM and 512GB SSD.',
                'price' => 1299.99,
                'image' => 'products/laptop.jpg',
                'featured' => true
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
