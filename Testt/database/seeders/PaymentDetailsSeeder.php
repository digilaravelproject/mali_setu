<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Models\Payment;
use App\Models\Donation;
use App\Models\DonationCause;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PaymentDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_IN');
        
        // Get users, businesses, and matrimony profiles
        $users = User::where('email_verified_at', '!=', null)->get();
        $businesses = Business::all();
        $matrimonyProfiles = MatrimonyProfile::all();
        
        // Create donation causes first
        $donationCauses = [
            [
                'title' => 'Mali Community Education Fund',
                'description' => 'Supporting education for underprivileged children in Mali community',
                'category' => 'Education',
                'target_amount' => 500000,
                'raised_amount' => 0,
                'urgency' => 'high',
                'location' => 'Mumbai, Maharashtra',
                'organization' => 'Mali Education Trust',
                'contact_info' => json_encode([
                    'email' => 'education@malisetu.com',
                    'phone' => '+91-9876543210',
                    'address' => 'Mumbai, Maharashtra'
                ]),
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(12)->format('Y-m-d'),
                'status' => 'active'
            ],
            [
                'title' => 'Healthcare Support Initiative',
                'description' => 'Providing medical assistance to families in need',
                'category' => 'Healthcare',
                'target_amount' => 300000,
                'raised_amount' => 0,
                'urgency' => 'critical',
                'location' => 'Pune, Maharashtra',
                'organization' => 'Mali Health Foundation',
                'contact_info' => json_encode([
                    'email' => 'health@malisetu.com',
                    'phone' => '+91-9876543211',
                    'address' => 'Pune, Maharashtra'
                ]),
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(6)->format('Y-m-d'),
                'status' => 'active'
            ],
            [
                'title' => 'Women Empowerment Program',
                'description' => 'Supporting women entrepreneurs and skill development',
                'category' => 'Social Welfare',
                'target_amount' => 200000,
                'raised_amount' => 0,
                'urgency' => 'medium',
                'location' => 'Nashik, Maharashtra',
                'organization' => 'Mali Women Welfare Society',
                'contact_info' => json_encode([
                    'email' => 'women@malisetu.com',
                    'phone' => '+91-9876543212',
                    'address' => 'Nashik, Maharashtra'
                ]),
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(18)->format('Y-m-d'),
                'status' => 'active'
            ],
            [
                'title' => 'Emergency Relief Fund',
                'description' => 'Emergency assistance for natural disasters and crises',
                'category' => 'Emergency',
                'target_amount' => 1000000,
                'raised_amount' => 0,
                'urgency' => 'critical',
                'location' => 'Maharashtra',
                'organization' => 'Mali Emergency Response Team',
                'contact_info' => json_encode([
                    'email' => 'emergency@malisetu.com',
                    'phone' => '+91-9876543213',
                    'address' => 'Maharashtra'
                ]),
                'start_date' => now()->format('Y-m-d'),
                'status' => 'active'
            ]
        ];
        
        foreach ($donationCauses as $cause) {
            DonationCause::create($cause);
        }
        
        $causes = DonationCause::all();
        
        // 1. Create donation payments (20 donations)
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $cause = $causes->random();
            $amount = $faker->randomElement([500, 1000, 2000, 5000, 10000]);
            $status = $faker->randomElement(['completed', 'completed', 'completed', 'pending', 'failed']);
            
            $payment = Payment::create([
                'user_id' => $user->id,
                'payment_id' => 'pay_' . $faker->unique()->bothify('??????????'),
                'order_id' => 'order_' . $faker->unique()->bothify('??????????'),
                'transaction_id' => 'TXN' . $faker->unique()->numerify('##########'),
                'payment_type' => 'donation',
                'amount' => $amount,
                'currency' => 'INR',
                'status' => $status,
                'payment_method' => $faker->randomElement(['upi', 'card', 'netbanking', 'wallet']),
                'description' => 'Donation for ' . $cause->title,
                'metadata' => json_encode([
                    'cause_id' => $cause->id,
                    'cause_title' => $cause->title,
                    'donor_name' => $user->name
                ]),
                'paid_at' => $status === 'completed' ? now() : null,
                'refunded_at' => $status === 'refunded' ? now() : null,
            ]);
            
            // Create corresponding donation record
            if ($status === 'completed') {
                Donation::create([
                    'user_id' => $user->id,
                    'cause_id' => $cause->id,
                    'amount' => $amount,
                    'currency' => 'INR',
                    'payment_method' => $payment->payment_method,
                    'razorpay_payment_id' => $payment->payment_id,
                    'razorpay_order_id' => $payment->order_id,
                    'status' => 'completed',
                    'anonymous' => $faker->boolean(20), // 20% chance of anonymous donation
                    'message' => $faker->optional(0.3)->sentence(), // 30% chance of message
                ]);
                
                // Update cause raised amount
                $cause->increment('raised_amount', $amount);
            }
        }
        
        // 2. Create business verification payments (8 payments)
        foreach ($businesses->take(8) as $business) {
            $status = $faker->randomElement(['completed', 'completed', 'pending']);
            
            Payment::create([
                'user_id' => $business->user_id,
                'payment_id' => 'pay_' . $faker->unique()->bothify('??????????'),
                'order_id' => 'order_' . $faker->unique()->bothify('??????????'),
                'transaction_id' => 'VER' . $faker->unique()->numerify('##########'),
                'payment_type' => 'business_registration',
                'amount' => 2500, // Standard verification fee
                'currency' => 'INR',
                'status' => $status,
                'payment_method' => $faker->randomElement(['upi', 'card', 'netbanking']),
                'description' => 'Business verification fee for ' . $business->business_name,
                'metadata' => json_encode([
                    'business_id' => $business->id,
                    'business_name' => $business->business_name,
                    'verification_type' => 'standard'
                ]),
                'paid_at' => $status === 'completed' ? now() : null,
            ]);
        }
        
        // 3. Create business subscription payments (10 payments)
        foreach ($businesses as $business) {
            $planType = $faker->randomElement(['basic', 'premium', 'enterprise']);
            $amount = match($planType) {
                'basic' => 999,
                'premium' => 2499,
                'enterprise' => 4999
            };
            $status = $faker->randomElement(['completed', 'completed', 'completed', 'pending']);
            
            Payment::create([
                'user_id' => $business->user_id,
                'payment_id' => 'pay_' . $faker->unique()->bothify('??????????'),
                'order_id' => 'order_' . $faker->unique()->bothify('??????????'),
                'transaction_id' => 'SUB' . $faker->unique()->numerify('##########'),
                'payment_type' => 'other',
                'amount' => $amount,
                'currency' => 'INR',
                'status' => $status,
                'payment_method' => $faker->randomElement(['upi', 'card', 'netbanking']),
                'description' => ucfirst($planType) . ' subscription for ' . $business->business_name,
                'metadata' => json_encode([
                    'business_id' => $business->id,
                    'plan_type' => $planType,
                    'duration' => '1 year',
                    'features' => $this->getSubscriptionFeatures($planType),
                    'purpose' => 'business_subscription'
                ]),
                'paid_at' => $status === 'completed' ? now() : null,
            ]);
        }
        
        // 4. Create matrimony premium membership payments (12 payments)
        foreach ($matrimonyProfiles->take(12) as $profile) {
            $planType = $faker->randomElement(['premium', 'gold', 'platinum']);
            $amount = match($planType) {
                'premium' => 1999,
                'gold' => 3999,
                'platinum' => 6999
            };
            $status = $faker->randomElement(['completed', 'completed', 'pending']);
            
            Payment::create([
                'user_id' => $profile->user_id,
                'payment_id' => 'pay_' . $faker->unique()->bothify('??????????'),
                'order_id' => 'order_' . $faker->unique()->bothify('??????????'),
                'transaction_id' => 'MAT' . $faker->unique()->numerify('##########'),
                'payment_type' => 'matrimony_subscription',
                'amount' => $amount,
                'currency' => 'INR',
                'status' => $status,
                'payment_method' => $faker->randomElement(['upi', 'card', 'netbanking']),
                'description' => ucfirst($planType) . ' matrimony membership',
                'metadata' => json_encode([
                    'profile_id' => $profile->id,
                    'plan_type' => $planType,
                    'duration' => '6 months',
                    'features' => $this->getMatrimonyFeatures($planType),
                    'purpose' => 'matrimony_premium'
                ]),
                'paid_at' => $status === 'completed' ? now() : null,
            ]);
        }
        
        // 5. Create some refunded payments (3 refunds)
        $completedPayments = Payment::where('status', 'completed')->take(3)->get();
        foreach ($completedPayments as $payment) {
            $refundAmount = $payment->amount * 0.8; // Partial refund
            
            $payment->update([
                'status' => 'refunded',
                'refund_amount' => $refundAmount,
                'refunded_at' => now(),
                'metadata' => json_encode(array_merge(
                    json_decode($payment->metadata, true) ?? [],
                    [
                        'refund_reason' => $faker->randomElement([
                            'Service not delivered',
                            'Customer request',
                            'Technical issue',
                            'Duplicate payment'
                        ]),
                        'refunded_by' => 1, // Admin user
                        'notes' => 'Refund processed as per customer request'
                    ]
                ))
            ]);
        }
        
        $this->command->info('Payment details created successfully!');
        $this->command->info('- 20 donation payments');
        $this->command->info('- 8 business verification payments');
        $this->command->info('- 10 business subscription payments');
        $this->command->info('- 12 matrimony premium payments');
        $this->command->info('- 3 refunded payments');
    }
    
    /**
     * Get subscription features based on plan type
     */
    private function getSubscriptionFeatures($planType): array
    {
        return match($planType) {
            'basic' => ['5 job postings', 'Basic analytics', 'Email support'],
            'premium' => ['15 job postings', 'Advanced analytics', 'Priority support', 'Featured listing'],
            'enterprise' => ['Unlimited job postings', 'Premium analytics', '24/7 support', 'Featured listing', 'Custom branding']
        };
    }
    
    /**
     * Get matrimony features based on plan type
     */
    private function getMatrimonyFeatures($planType): array
    {
        return match($planType) {
            'premium' => ['50 profile views', 'Direct messaging', 'Profile highlighting'],
            'gold' => ['100 profile views', 'Direct messaging', 'Profile highlighting', 'Priority matching'],
            'platinum' => ['Unlimited profile views', 'Direct messaging', 'Profile highlighting', 'Priority matching', 'Dedicated support']
        };
    }
}