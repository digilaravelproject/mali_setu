<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Mali Setu Database Seeding...');
        
        // Seed in specific order to maintain referential integrity
        $this->call([
            AdminUserSeeder::class,
            UsersSeeder::class,
            BusinessSeeder::class,
            JobListingsSeeder::class,
            PaymentDetailsSeeder::class,
        ]);
        
        $this->command->info('✅ Mali Setu Database Seeding Completed Successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('- 1 Admin user (admin@malisetu.com)');
        $this->command->info('- 35 Users (5 general, 5 volunteers, 10 matrimony, 5 business, 10 unverified)');
        $this->command->info('- 10 Businesses with products and services');
        $this->command->info('- 10 Job listings');
        $this->command->info('- 53 Payment records (donations, subscriptions, verifications)');
        $this->command->info('');
        $this->command->info('🔐 Admin Login: admin@malisetu.com / admin123');
    }
}
