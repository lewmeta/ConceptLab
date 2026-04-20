<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                // Ensure idempotent seeding by using a unique identifier (email) in the lookup.
                'email' => 'admin@conceptlab.com',
            ],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'role' => UserRole::Admin,
            ]
        );
    }
}
