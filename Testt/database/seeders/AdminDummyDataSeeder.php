<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Models\Product;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\CasteCertificate;
use App\Models\ConnectionRequest;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Carbon\Carbon;

class AdminDummyDataSeeder extends Seeder
{
    public function run()
    {
        // Create Admin Users (check if they exist first)
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@malisetu.com'],
            [
                'name' => 'Super Admin',
                'phone' => '+919999999999',
                'password' => Hash::make('admin123'),
                'user_type' => 'general',
                'caste_verification_status' => 'approved',
                'email_verified_at' => now(),
            ]
        );

        $moderator = User::firstOrCreate(
            ['email' => 'moderator@malisetu.com'],
            [
                'name' => 'Moderator User',
                'phone' => '+919999999998',
                'password' => Hash::make('moderator123'),
                'user_type' => 'general',
                'caste_verification_status' => 'approved',
                'email_verified_at' => now(),
            ]
        );

        // Create General Users
        $generalUsers = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "General User {$i}",
                    'phone' => "+9199999999" . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password123'),
                    'user_type' => 'general',
                    'caste_verification_status' => $i <= 8 ? 'approved' : 'pending',
                    'email_verified_at' => now(),
                ]
            );
            $generalUsers[] = $user;

            // Create caste certificates
            CasteCertificate::create([
                'user_id' => $user->id,
                'file_path' => "certificates/caste_certificate_{$user->id}.pdf",
                'verification_status' => $i <= 8 ? 'approved' : 'pending',
                'admin_notes' => $i <= 8 ? 'Certificate verified successfully' : null,
                'verified_by' => $i <= 8 ? $superAdmin->id : null,
                'verified_at' => $i <= 8 ? now() : null,
            ]);
        }

        // Create Business Users and Businesses
        $businessUsers = [];
        $businesses = [];
        $businessCategories = [
            ['name' => 'Tea', 'description' => 'Tea shops and vendors'],
            ['name' => 'Grocery', 'description' => 'Grocery stores and supermarkets'],
            ['name' => 'Restaurant', 'description' => 'Restaurants and food services'],
            ['name' => 'Medical', 'description' => 'Healthcare and medical services'],
            ['name' => 'Education', 'description' => 'Educational institutions'],
            ['name' => 'Electronics', 'description' => 'Electronics and mobile services'],
        ];

        // Insert business categories (check if they exist first)
        foreach ($businessCategories as $index => $category) {
            DB::table('business_categories')->insertOrIgnore([
                'id' => $index + 1,
                'name' => $category['name'],
                'description' => $category['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 1; $i <= 15; $i++) {
            $user = User::firstOrCreate(
                ['email' => "business{$i}@example.com"],
                [
                    'name' => "Business Owner {$i}",
                    'phone' => "+9188888888" . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password123'),
                    'user_type' => 'business',
                    'caste_verification_status' => $i <= 12 ? 'approved' : 'pending',
                    'email_verified_at' => now(),
                ]
            );
            $businessUsers[] = $user;

            // Create caste certificate
            CasteCertificate::create([
                'user_id' => $user->id,
                'file_path' => "certificates/caste_certificate_{$user->id}.pdf",
                'verification_status' => $i <= 12 ? 'approved' : 'pending',
                'admin_notes' => $i <= 12 ? 'Certificate verified successfully' : null,
                'verified_by' => $i <= 12 ? $superAdmin->id : null,
                'verified_at' => $i <= 12 ? now() : null,
            ]);

            if ($i <= 12) {
                $business = Business::create([
                    'user_id' => $user->id,
                    'business_name' => "Business {$i}",
                    'business_type' => $i % 2 == 0 ? 'product' : 'service',
                    'category_id' => ($i % 6) + 1,
                    'description' => "This is a sample business description for Business {$i}",
                    'contact_phone' => $user->phone,
                    'contact_email' => $user->email,
                    'website' => "https://business{$i}.com",
                    'verification_status' => $i <= 10 ? 'approved' : 'pending',
                    'subscription_status' => 'active',
                    'subscription_expires_at' => now()->addMonths(6),
                    'job_posting_limit' => 5,
                ]);
                $businesses[] = $business;

                // Create products for product-based businesses
                if ($business->business_type === 'product') {
                    for ($j = 1; $j <= 3; $j++) {
                        Product::create([
                            'business_id' => $business->id,
                            'name' => "Product {$j} from {$business->business_name}",
                            'description' => "High quality product {$j} with excellent features",
                            'cost' => rand(100, 5000),
                            'image_path' => "products/product_{$business->id}_{$j}.jpg",
                            'status' => 'active',
                        ]);
                    }
                }

                // Create services for service-based businesses
                if ($business->business_type === 'service') {
                    for ($j = 1; $j <= 3; $j++) {
                        Service::create([
                            'business_id' => $business->id,
                            'name' => "Service {$j} from {$business->business_name}",
                            'description' => "Professional service {$j} with guaranteed satisfaction",
                            'cost' => rand(500, 10000),
                            'image_path' => "services/service_{$business->id}_{$j}.jpg",
                            'status' => 'active',
                        ]);
                    }
                }

                // Create transaction for business registration
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => 500.00,
                    'currency' => 'INR',
                    'purpose' => 'business_registration',
                    'razorpay_payment_id' => 'pay_' . uniqid(),
                    'razorpay_order_id' => 'order_' . uniqid(),
                    'status' => 'completed',
                    'subscription_period' => 6,
                    'receipt_url' => "receipts/business_registration_{$user->id}.pdf",
                ]);
            }
        }

        // Create Matrimony Users and Profiles
        $matrimonyUsers = [];
        for ($i = 1; $i <= 20; $i++) {
            $user = User::firstOrCreate(
                ['email' => "matrimony{$i}@example.com"],
                [
                    'name' => "Matrimony User {$i}",
                    'phone' => "+9177777777" . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password123'),
                    'user_type' => 'matrimony',
                    'caste_verification_status' => $i <= 16 ? 'approved' : 'pending',
                    'email_verified_at' => now(),
                ]
            );
            $matrimonyUsers[] = $user;

            // Create caste certificate
            CasteCertificate::create([
                'user_id' => $user->id,
                'file_path' => "certificates/caste_certificate_{$user->id}.pdf",
                'verification_status' => $i <= 16 ? 'approved' : 'pending',
                'admin_notes' => $i <= 16 ? 'Certificate verified successfully' : null,
                'verified_by' => $i <= 16 ? $superAdmin->id : null,
                'verified_at' => $i <= 16 ? now() : null,
            ]);

            if ($i <= 16) {
                $profile = MatrimonyProfile::create([
                    'user_id' => $user->id,
                    'age' => rand(22, 35),
                    'height' => rand(150, 180) . ' cm',
                    'weight' => rand(50, 80) . ' kg',
                    'complexion' => ['Fair', 'Medium', 'Dark'][rand(0, 2)],
                    'physical_status' => 'Normal',
                    'personal_details' => json_encode([
                        'marital_status' => 'Never Married',
                        'diet' => ['Vegetarian', 'Non-Vegetarian'][rand(0, 1)],
                        'smoking' => 'No',
                        'drinking' => 'No',
                    ]),
                    'family_details' => json_encode([
                        'father_occupation' => ['Business', 'Service', 'Retired'][rand(0, 2)],
                        'mother_occupation' => ['Housewife', 'Service', 'Business'][rand(0, 2)],
                        'siblings' => rand(0, 3),
                        'family_type' => ['Nuclear', 'Joint'][rand(0, 1)],
                    ]),
                    'education_details' => json_encode([
                        'qualification' => ['Graduate', 'Post Graduate', 'Professional'][rand(0, 2)],
                        'institution' => 'Sample University',
                        'specialization' => ['Engineering', 'Commerce', 'Arts'][rand(0, 2)],
                    ]),
                    'professional_details' => json_encode([
                        'occupation' => ['Software Engineer', 'Teacher', 'Doctor', 'Business'][rand(0, 3)],
                        'income' => rand(300000, 1500000),
                        'company' => 'Sample Company',
                    ]),
                    'lifestyle_details' => json_encode([
                        'hobbies' => ['Reading', 'Music', 'Sports', 'Travel'],
                        'interests' => ['Technology', 'Arts', 'Social Work'],
                    ]),
                    'location_details' => json_encode([
                        'current_city' => ['Mumbai', 'Delhi', 'Bangalore', 'Pune'][rand(0, 3)],
                        'hometown' => ['Mumbai', 'Delhi', 'Bangalore', 'Pune'][rand(0, 3)],
                        'willing_to_relocate' => [true, false][rand(0, 1)],
                    ]),
                    'partner_preferences' => json_encode([
                        'age_min' => 22,
                        'age_max' => 35,
                        'height_min' => '150 cm',
                        'height_max' => '180 cm',
                        'education' => 'Graduate and above',
                        'occupation' => 'Any',
                    ]),
                    'privacy_settings' => json_encode([
                        'photo_visibility' => 'public',
                        'contact_visibility' => 'premium',
                        'profile_visibility' => 'active',
                    ]),
                    'approval_status' => $i <= 14 ? 'approved' : 'pending',
                    'profile_expires_at' => now()->addMonths(12),
                ]);

                // Create transaction for matrimony profile
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => 300.00,
                    'currency' => 'INR',
                    'purpose' => 'matrimony_profile',
                    'razorpay_payment_id' => 'pay_' . uniqid(),
                    'razorpay_order_id' => 'order_' . uniqid(),
                    'status' => 'completed',
                    'subscription_period' => 12,
                    'receipt_url' => "receipts/matrimony_profile_{$user->id}.pdf",
                ]);
            }
        }

        // Create Connection Requests between matrimony users
        for ($i = 0; $i < 10; $i++) {
            $sender = $matrimonyUsers[rand(0, 15)];
            $receiver = $matrimonyUsers[rand(0, 15)];
            
            if ($sender->id !== $receiver->id) {
                $connectionRequest = ConnectionRequest::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'status' => ['pending', 'accepted', 'rejected'][rand(0, 2)],
                    'message' => 'I am interested in your profile. Would like to connect.',
                    'response_message' => rand(0, 1) ? 'Thank you for your interest. Happy to connect.' : null,
                    'responded_at' => rand(0, 1) ? now() : null,
                ]);

                // Create chat conversation for accepted requests
                if ($connectionRequest->status === 'accepted') {
                    $conversation = ChatConversation::create([
                        'user1_id' => min($sender->id, $receiver->id),
                        'user2_id' => max($sender->id, $receiver->id),
                        'last_message_at' => now(),
                    ]);

                    // Create some chat messages
                    for ($j = 1; $j <= rand(3, 8); $j++) {
                        ChatMessage::create([
                            'conversation_id' => $conversation->id,
                            'sender_id' => rand(0, 1) ? $sender->id : $receiver->id,
                            'message_text' => "Sample message {$j} in conversation",
                            'message_type' => 'text',
                            'is_read' => rand(0, 1),
                            'created_at' => now()->subMinutes(rand(1, 1440)),
                        ]);
                    }
                }
            }
        }

        // Create Volunteer Users
        for ($i = 1; $i <= 8; $i++) {
            $user = User::firstOrCreate(
                ['email' => "volunteer{$i}@example.com"],
                [
                    'name' => "Volunteer {$i}",
                    'phone' => "+9166666666" . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password123'),
                    'user_type' => 'volunteer',
                    'caste_verification_status' => $i <= 6 ? 'approved' : 'pending',
                    'email_verified_at' => now(),
                ]
            );

            // Create caste certificate
            CasteCertificate::create([
                'user_id' => $user->id,
                'file_path' => "certificates/caste_certificate_{$user->id}.pdf",
                'verification_status' => $i <= 6 ? 'approved' : 'pending',
                'admin_notes' => $i <= 6 ? 'Certificate verified successfully' : null,
                'verified_by' => $i <= 6 ? $superAdmin->id : null,
                'verified_at' => $i <= 6 ? now() : null,
            ]);
        }

        // Create some donation transactions
        for ($i = 1; $i <= 5; $i++) {
            $user = $generalUsers[rand(0, 7)];
            Transaction::create([
                'user_id' => $user->id,
                'amount' => rand(100, 5000),
                'currency' => 'INR',
                'purpose' => 'donation',
                'razorpay_payment_id' => 'pay_' . uniqid(),
                'razorpay_order_id' => 'order_' . uniqid(),
                'status' => 'completed',
                'receipt_url' => "receipts/donation_{$user->id}_{$i}.pdf",
            ]);
        }

        // Insert system settings (check if they exist first)
        $systemSettings = [
            ['key_name' => 'business_registration_fee', 'value' => '500.00', 'description' => 'Fee for business registration in INR'],
            ['key_name' => 'matrimony_profile_fee', 'value' => '300.00', 'description' => 'Fee for matrimony profile in INR'],
            ['key_name' => 'trial_period_days', 'value' => '7', 'description' => 'Trial period duration in days'],
            ['key_name' => 'max_job_postings', 'value' => '5', 'description' => 'Maximum job postings per business'],
            ['key_name' => 'app_name', 'value' => 'Mali Setu', 'description' => 'Application name'],
            ['key_name' => 'support_email', 'value' => 'support@malisetu.com', 'description' => 'Support email address'],
        ];
        
        foreach ($systemSettings as $setting) {
            DB::table('system_settings')->insertOrIgnore($setting);
        }

        $this->command->info('Admin dummy data seeded successfully!');
    }
}