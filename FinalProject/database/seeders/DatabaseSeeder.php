<?php

namespace Database\Seeders;

use App\Models\User;
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
        // User::factory(10)->create();

        $this->call([
            ArtistSeeder::class,
            StyleSeeder::class,
            ArtworkSeeder::class,
        ]);
        
        // Regular User
        User::create([
            'name' => 'Guest',
            'email' => 'guest@test.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'role' => 'admin',
        ]);
    }
}
