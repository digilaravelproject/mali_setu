<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\User;
use App\Models\Business;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and businesses for relationships
        $users = User::limit(10)->get();
        $businesses = Business::limit(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }
        
        $paymentTypes = ['business_registration', 'matrimony_subscription', 'donation', 'other'];
        $paymentMethods = ['razorpay', 'upi', 'card', 'netbanking', 'wallet'];
        $statuses = ['completed', 'pending', 'failed', 'refunded'];
        
        // Create sample payments
        for ($i = 1; $i <= 50; $i++) {
            $user = $users->random();
            $business = $businesses->isNotEmpty() ? $businesses->random() : null;
            $paymentType = $paymentTypes[array_rand($paymentTypes)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $status = $statuses[array_rand($statuses)];
            
            // Generate realistic amounts based on payment type
            $amount = match($paymentType) {
                'business_registration' => rand(500, 2000),
                'matrimony_subscription' => rand(999, 4999),
                'donation' => rand(100, 5000),
                'other' => rand(100, 1000),
                default => rand(100, 1000)
            };
            
            $createdAt = Carbon::now()->subDays(rand(1, 90));
            
            Payment::create([
                'user_id' => $user->id,
                'payment_id' => 'pay_' . strtoupper(uniqid()),
                'order_id' => 'order_' . strtoupper(uniqid()),
                'transaction_id' => 'TXN' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'amount' => $amount,
                'currency' => 'INR',
                'payment_type' => $paymentType,
                'payment_method' => $paymentMethod,
                'status' => $status,
                'razorpay_response' => json_encode([
                    'razorpay_payment_id' => 'pay_' . strtoupper(uniqid()),
                    'razorpay_order_id' => 'order_' . strtoupper(uniqid()),
                    'razorpay_signature' => hash('sha256', uniqid())
                ]),
                'metadata' => json_encode([
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]),
                'description' => ucfirst(str_replace('_', ' ', $paymentType)) . ' payment',
                'paid_at' => $status === 'completed' ? $createdAt : null,
                'receipt_number' => $status === 'completed' ? 'RCP' . str_pad($i, 6, '0', STR_PAD_LEFT) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
        
        $this->command->info('Payment records seeded successfully!');
    }
}