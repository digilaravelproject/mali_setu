<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MatrimonyProfile;
use App\Models\VolunteerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_IN');
        
        // Indian names arrays
        $maleNames = ['Rajesh Kumar', 'Amit Sharma', 'Suresh Patel', 'Vikram Singh', 'Ravi Gupta', 'Manoj Verma', 'Deepak Joshi', 'Sanjay Agarwal', 'Ashok Yadav', 'Ramesh Tiwari'];
        $femaleNames = ['Priya Sharma', 'Sunita Patel', 'Kavita Singh', 'Meera Gupta', 'Anjali Verma', 'Pooja Joshi', 'Rekha Agarwal', 'Sita Yadav', 'Geeta Tiwari', 'Radha Kumar'];
        $indianCities = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata', 'Hyderabad', 'Pune', 'Ahmedabad', 'Jaipur', 'Lucknow', 'Kanpur', 'Nagpur', 'Indore', 'Bhopal', 'Visakhapatnam'];
        
        // 1. Create 5 regular users
        for ($i = 0; $i < 5; $i++) {
            $name = $faker->randomElement(array_merge($maleNames, $femaleNames));
            $email = strtolower(str_replace(' ', '.', $name)) . $i . '@gmail.com';
            
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'phone' => '+91-' . $faker->numerify('##########'),
                'user_type' => 'general',
                'caste_verification_status' => 'approved',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }
        
        // 2. Create 5 volunteers with profiles
        for ($i = 0; $i < 5; $i++) {
            $name = $faker->randomElement(array_merge($maleNames, $femaleNames));
            $email = 'volunteer' . ($i + 1) . '@malisetu.com';
            
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'phone' => '+91-' . $faker->numerify('##########'),
                'user_type' => 'volunteer',
                'caste_verification_status' => 'approved',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            
            // Create volunteer profile
            VolunteerProfile::create([
                'user_id' => $user->id,
                'skills' => 'Community Service, Event Management, Teaching, Healthcare Support',
                'availability' => 'weekends',
                'experience' => $faker->numberBetween(1, 10) . ' years',
                'bio' => 'Dedicated volunteer committed to serving the Mali community.',
                'location' => $faker->randomElement($indianCities),
                'interests' => ['Community Development', 'Education', 'Healthcare'],
                'status' => 'active',
            ]);
        }
        
        // 3. Create 10 matrimony profiles
        for ($i = 0; $i < 10; $i++) {
            $gender = $i % 2 == 0 ? 'male' : 'female';
            $name = $gender == 'male' ? $faker->randomElement($maleNames) : $faker->randomElement($femaleNames);
            $email = 'matrimony' . ($i + 1) . '@malisetu.com';
            
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'phone' => '+91-' . $faker->numerify('##########'),
                'user_type' => 'matrimony',
                'caste_verification_status' => 'approved',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            
            // Create matrimony profile
            MatrimonyProfile::create([
                'user_id' => $user->id,
                'age' => $faker->numberBetween(22, 35),
                'height' => $faker->numberBetween(150, 185) . ' cm',
                'weight' => $faker->numberBetween(45, 90) . ' kg',
                'complexion' => $faker->randomElement(['Fair', 'Medium', 'Dark']),
                'physical_status' => 'Normal',
                'personal_details' => [
                    'gender' => $gender,
                    'marital_status' => 'single',
                    'religion' => 'Hindu',
                    'caste' => 'Mali',
                    'subcaste' => $faker->randomElement(['Mali Patil', 'Mali Koli', 'Mali Kunbi']),
                    'mother_tongue' => 'Marathi',
                    'about_me' => 'Looking for a life partner from Mali community with similar values and interests.'
                ],
                'education_details' => [
                    'highest_education' => $faker->randomElement(['Graduate', 'Post Graduate', 'Professional', 'Doctorate']),
                    'college' => $faker->company . ' College',
                    'field_of_study' => $faker->randomElement(['Engineering', 'Commerce', 'Arts', 'Science', 'Medicine'])
                ],
                'professional_details' => [
                    'occupation' => $faker->randomElement(['Software Engineer', 'Teacher', 'Doctor', 'Business Owner', 'Government Employee']),
                    'company' => $faker->company,
                    'annual_income' => $faker->randomElement(['3-5 Lakhs', '5-8 Lakhs', '8-12 Lakhs', '12-20 Lakhs', '20+ Lakhs'])
                ],
                'family_details' => [
                    'family_type' => $faker->randomElement(['Nuclear', 'Joint']),
                    'father_occupation' => $faker->randomElement(['Farmer', 'Business', 'Service', 'Retired']),
                    'mother_occupation' => $faker->randomElement(['Homemaker', 'Teacher', 'Service', 'Business']),
                    'siblings' => $faker->numberBetween(0, 3)
                ],
                'location_details' => [
                    'city' => $faker->randomElement($indianCities),
                    'state' => 'Maharashtra',
                    'country' => 'India'
                ],
                'lifestyle_details' => [
                    'diet' => $faker->randomElement(['Vegetarian', 'Non-Vegetarian']),
                    'smoking' => 'No',
                    'drinking' => $faker->randomElement(['No', 'Occasionally'])
                ],
                'partner_preferences' => [
                    'age_range' => '22-30',
                    'height_range' => '155-175',
                    'education' => 'Graduate+',
                    'location' => 'Any',
                    'caste' => 'Mali'
                ],
                'privacy_settings' => [
                    'show_contact' => false,
                    'show_photos' => true,
                    'profile_visibility' => 'public'
                ],
                'approval_status' => 'approved',
                'profile_expires_at' => now()->addYear(),
            ]);
        }
        
        // 4. Create 5 business owners
        for ($i = 0; $i < 5; $i++) {
            $name = $faker->randomElement($maleNames);
            $email = 'business' . ($i + 1) . '@malisetu.com';
            
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'phone' => '+91-' . $faker->numerify('##########'),
                'user_type' => 'business',
                'caste_verification_status' => 'approved',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }
        
        // 5. Create 10 non-verified users (mixed roles)
        $unverifiedRoles = ['general', 'business', 'matrimony', 'volunteer'];
        for ($i = 0; $i < 10; $i++) {
            $name = $faker->randomElement(array_merge($maleNames, $femaleNames));
            $email = 'unverified' . ($i + 1) . '@example.com';
            
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'phone' => '+91-' . $faker->numerify('##########'),
                'user_type' => $faker->randomElement($unverifiedRoles),
                'caste_verification_status' => 'pending',
                'status' => 'active',
                'email_verified_at' => null, // Not verified
            ]);
        }
        
        $this->command->info('35 users created successfully!');
    }
}