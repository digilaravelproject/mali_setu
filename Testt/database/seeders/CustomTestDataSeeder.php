<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Models\VolunteerProfile;
use App\Models\Transaction;
use App\Models\BusinessCategory;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomTestDataSeeder extends Seeder
{
    public function run()
    {
        // Create 20 users with specific distribution
        $this->createUsers();
        
        // Create 5 businesses
        $this->createBusinesses();
        
        // Create 10 payment transactions
        $this->createPaymentTransactions();
        
        $this->command->info('Custom test data seeded successfully!');
    }
    
    private function createUsers()
    {
        $timestamp = time();
        
        // Create 5 individual users
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Individual User {$i}",
                'email' => "individual{$i}_{$timestamp}@malisetu.com",
                'password' => Hash::make('password123'),
                'phone' => '987654' . str_pad(($timestamp + $i) % 10000, 4, '0', STR_PAD_LEFT),
                'user_type' => 'individual',
                'caste_verification_status' => $i <= 2 ? 'pending' : 'approved',
                'email_verified_at' => now(),
                'status' => 'active'
            ]);
        }
        
        // Create 5 matrimony users with profiles
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Matrimony User {$i}",
                'email' => "matrimony{$i}_{$timestamp}@malisetu.com",
                'password' => Hash::make('password123'),
                'phone' => '987655' . str_pad(($timestamp + $i + 10) % 10000, 4, '0', STR_PAD_LEFT),
                'user_type' => 'matrimony',
                'caste_verification_status' => $i <= 2 ? 'pending' : 'approved',
                'email_verified_at' => now(),
                'status' => 'active'
            ]);
            
            // Create matrimony profile
            MatrimonyProfile::create([
                'user_id' => $user->id,
                'age' => rand(22, 35),
                'height' => rand(150, 180) . ' cm',
                'weight' => rand(50, 80) . ' kg',
                'complexion' => ['Fair', 'Medium', 'Dark'][rand(0, 2)],
                'physical_status' => 'Normal',
                'personal_details' => json_encode([
                    'gender' => $i % 2 == 0 ? 'male' : 'female',
                    'marital_status' => 'single',
                    'religion' => 'Hindu',
                    'caste' => 'Mali',
                    'subcaste' => 'Mali Subcaste',
                    'gotra' => 'Gotra ' . $i,
                    'bio' => "Bio for matrimony user {$i}"
                ]),
                'family_details' => json_encode([
                    'father_name' => 'Father ' . $i,
                    'mother_name' => 'Mother ' . $i,
                    'siblings' => rand(0, 3),
                    'family_type' => 'Nuclear'
                ]),
                'education_details' => json_encode([
                    'highest_qualification' => ['Graduate', 'Post Graduate', 'Professional'][rand(0, 2)],
                    'college' => 'College ' . $i,
                    'specialization' => 'Specialization ' . $i
                ]),
                'professional_details' => json_encode([
                    'occupation' => ['Engineer', 'Doctor', 'Teacher', 'Business'][rand(0, 3)],
                    'company' => 'Company ' . $i,
                    'income' => rand(300000, 1000000)
                ]),
                'location_details' => json_encode([
                    'city' => 'City ' . $i,
                    'state' => 'State ' . $i,
                    'country' => 'India'
                ]),
                'partner_preferences' => json_encode([
                    'age_range' => '22-35',
                    'education' => 'Graduate+',
                    'occupation' => 'Any',
                    'location' => 'Any'
                ]),
                'approval_status' => $i <= 2 ? 'pending' : 'approved'
            ]);
        }
        
        // Create 5 business users with profiles
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Business Owner {$i}",
                'email' => "business{$i}_{$timestamp}@malisetu.com",
                'password' => Hash::make('password123'),
                'phone' => '987656' . str_pad(($timestamp + $i + 20) % 10000, 4, '0', STR_PAD_LEFT),
                'user_type' => 'business',
                'caste_verification_status' => 'approved',
                'email_verified_at' => now(),
                'status' => 'active'
            ]);
        }
        
        // Create 5 volunteer users with profiles
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Volunteer {$i}",
                'email' => "volunteer{$i}_{$timestamp}@malisetu.com",
                'password' => Hash::make('password123'),
                'phone' => '987657' . str_pad(($timestamp + $i + 30) % 10000, 4, '0', STR_PAD_LEFT),
                'user_type' => 'volunteer',
                'caste_verification_status' => $i == 5 ? 'pending' : 'approved',
                'email_verified_at' => now(),
                'status' => 'active'
            ]);
            
            // Create volunteer profile
            VolunteerProfile::create([
                'user_id' => $user->id,
                'skills' => json_encode(['Community Service', 'Event Management', 'Social Work']),
                'availability' => json_encode(['weekends', 'evenings']),
                'experience' => "Volunteer experience for user {$i}",
                'interests' => json_encode(['Education', 'Healthcare', 'Environment']),
                'status' => 'active'
            ]);
        }
    }
    
    private function createBusinesses()
    {
        $timestamp = time();
        $businessUsers = User::where('user_type', 'business')->take(5)->get();
        $categories = BusinessCategory::take(3)->get();
        if ($categories->isEmpty()) {
            // Create some categories if none exist
            $categories = collect([
                BusinessCategory::create(['name' => 'General Services', 'description' => 'General business services']),
                BusinessCategory::create(['name' => 'Food & Beverage', 'description' => 'Food and beverage businesses']),
                BusinessCategory::create(['name' => 'Retail', 'description' => 'Retail businesses'])
            ]);
        }
        
        foreach ($businessUsers as $index => $user) {
            $business = Business::create([
                'user_id' => $user->id,
                'business_name' => "Mali Business " . ($index + 1),
                'business_type' => $index % 2 == 0 ? 'product' : 'service',
                'category_id' => $categories->random()->id,
                'description' => "Description for Mali Business " . ($index + 1),
                'contact_phone' => '987658' . str_pad(($timestamp + $index + 40) % 10000, 4, '0', STR_PAD_LEFT),
                'contact_email' => "business" . ($index + 1) . "_{$timestamp}@malisetu.com",
                'website' => "https://business" . ($index + 1) . ".malisetu.com",
                'verification_status' => $index < 2 ? 'pending' : 'approved',
                'subscription_status' => 'active',
                'job_posting_limit' => 5
            ]);
            
            // Create products for business
            for ($p = 1; $p <= 2; $p++) {
                Product::create([
                    'business_id' => $business->id,
                    'name' => "Product {$p} from Business " . ($index + 1),
                    'description' => "Description for product {$p}",
                    'cost' => rand(100, 1000),
                    'image_path' => 'products/default-product.jpg',
                    'status' => 'active'
                ]);
            }
            
            // Create services for business
            for ($s = 1; $s <= 2; $s++) {
                Service::create([
                    'business_id' => $business->id,
                    'name' => "Service {$s} from Business " . ($index + 1),
                    'description' => "Description for service {$s}",
                    'cost' => rand(500, 2000),
                    'image_path' => 'services/default-service.jpg',
                    'status' => 'active'
                ]);
            }
        }
    }
    
    private function createPaymentTransactions()
    {
        $users = User::take(15)->get(); // Get 15 users for transactions
        
        // Create 10 successful payment transactions
        for ($i = 1; $i <= 10; $i++) {
            Transaction::create([
                'user_id' => $users->random()->id,
                'amount' => rand(100, 1000),
                'currency' => 'INR',
                'purpose' => ['business_registration', 'matrimony_profile', 'donation'][rand(0, 2)],
                'status' => 'completed',
                'razorpay_payment_id' => 'pay_' . Str::random(14),
                'razorpay_order_id' => 'order_' . Str::random(14),
                'subscription_period' => rand(1, 12),
                'created_at' => now()->subDays(rand(1, 30))
            ]);
        }
        
        // Create 5 pending payment transactions
        for ($i = 1; $i <= 5; $i++) {
            Transaction::create([
                'user_id' => $users->random()->id,
                'amount' => 299, // Subscription amount
                'currency' => 'INR',
                'purpose' => 'business_registration',
                'status' => 'pending',
                'subscription_period' => 12,
                'created_at' => now()->subDays(rand(1, 7))
            ]);
        }
    }
}