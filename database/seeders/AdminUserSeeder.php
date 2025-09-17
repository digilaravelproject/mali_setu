<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Mali Setu Admin',
            'email' => 'admin@malisetu.com',
            'password' => Hash::make('admin123'),
            'phone' => '+91-9876543210',
            'user_type' => 'general', // Using general type for admin user
            'caste_verification_status' => 'approved',
            'status' => 'active',
            'email_verified_at' => now(),
            'admin_notes' => 'System Administrator Account - Has admin privileges',
        ]);

        $this->command->info('Admin user created successfully!');
    }
}