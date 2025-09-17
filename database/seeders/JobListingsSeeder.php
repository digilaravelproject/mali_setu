<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\JobPosting;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class JobListingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_IN');
        
        // Get approved businesses
        $businesses = Business::where('verification_status', 'approved')->get();
        
        if ($businesses->isEmpty()) {
            $this->command->warn('No approved businesses found. Creating job postings for all businesses.');
            $businesses = Business::all();
        }
        
        $indianCities = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata', 'Hyderabad', 'Pune', 'Ahmedabad', 'Jaipur', 'Lucknow'];
        
        // Job posting templates
        $jobTemplates = [
            [
                'title' => 'Sales Executive',
                'description' => 'We are looking for a dynamic Sales Executive to join our team. The candidate will be responsible for generating leads, meeting clients, and achieving sales targets.',
                'requirements' => 'Graduate with 2+ years of sales experience. Good communication skills in Hindi, Marathi, and English. Own vehicle preferred.',
                'category' => 'Sales & Marketing',
                'skills' => ['Sales', 'Communication', 'Client Management', 'Lead Generation'],
                'benefits' => ['Health Insurance', 'Performance Bonus', 'Travel Allowance'],
                'employment_type' => 'full_time',
                'experience_level' => 'mid_level'
            ],
            [
                'title' => 'Delivery Boy',
                'description' => 'Looking for reliable delivery personnel for our food delivery service. Must have own two-wheeler and valid driving license.',
                'requirements' => '10th pass minimum. Own two-wheeler with valid license. Knowledge of local area. Age 18-35 years.',
                'category' => 'Delivery & Logistics',
                'skills' => ['Driving', 'Time Management', 'Customer Service'],
                'benefits' => ['Fuel Allowance', 'Incentives', 'Flexible Hours'],
                'employment_type' => 'part_time',
                'experience_level' => 'entry_level'
            ],
            [
                'title' => 'Store Manager',
                'description' => 'Experienced Store Manager required for our retail outlet. Responsible for inventory management, staff supervision, and customer service.',
                'requirements' => 'Graduate with 3+ years retail experience. Strong leadership and organizational skills. Computer literacy required.',
                'category' => 'Retail & Management',
                'skills' => ['Management', 'Inventory Control', 'Leadership', 'Customer Service'],
                'benefits' => ['Health Insurance', 'PF', 'Annual Bonus', 'Career Growth'],
                'employment_type' => 'full_time',
                'experience_level' => 'senior_level'
            ],
            [
                'title' => 'Farm Worker',
                'description' => 'Seeking dedicated farm workers for agricultural operations. Work includes planting, harvesting, and general farm maintenance.',
                'requirements' => 'Experience in farming preferred. Physical fitness required. Willingness to work in outdoor conditions.',
                'category' => 'Agriculture',
                'skills' => ['Farming', 'Physical Work', 'Equipment Operation'],
                'benefits' => ['Accommodation', 'Meals', 'Seasonal Bonus'],
                'employment_type' => 'contract',
                'experience_level' => 'entry_level'
            ],
            [
                'title' => 'Cook/Chef',
                'description' => 'Experienced cook required for our catering business. Must know traditional Maharashtrian cuisine and modern cooking techniques.',
                'requirements' => 'Minimum 2 years cooking experience. Knowledge of Maharashtrian cuisine essential. Food safety certification preferred.',
                'category' => 'Food & Hospitality',
                'skills' => ['Cooking', 'Food Safety', 'Menu Planning', 'Kitchen Management'],
                'benefits' => ['Accommodation', 'Meals', 'Festival Bonus'],
                'employment_type' => 'full_time',
                'experience_level' => 'mid_level'
            ],
            [
                'title' => 'Accountant',
                'description' => 'Looking for qualified accountant to handle books of accounts, GST filing, and financial reporting for our business.',
                'requirements' => 'B.Com/M.Com with 2+ years experience. Knowledge of Tally, GST, and income tax. CA inter preferred.',
                'category' => 'Finance & Accounting',
                'skills' => ['Accounting', 'Tally', 'GST', 'Financial Reporting'],
                'benefits' => ['Health Insurance', 'PF', 'Professional Development'],
                'employment_type' => 'full_time',
                'experience_level' => 'mid_level'
            ],
            [
                'title' => 'Driver',
                'description' => 'Experienced driver required for goods transportation. Must have heavy vehicle license and clean driving record.',
                'requirements' => 'Valid heavy vehicle license. 3+ years driving experience. Clean driving record. Age 25-45 years.',
                'category' => 'Transportation',
                'skills' => ['Driving', 'Vehicle Maintenance', 'Route Planning'],
                'benefits' => ['Fuel Allowance', 'Overtime Pay', 'Insurance'],
                'employment_type' => 'full_time',
                'experience_level' => 'mid_level'
            ],
            [
                'title' => 'Tailor',
                'description' => 'Skilled tailor needed for our textile business. Must be proficient in both traditional and modern stitching techniques.',
                'requirements' => 'Minimum 3 years tailoring experience. Knowledge of different fabrics and stitching techniques. Own sewing machine preferred.',
                'category' => 'Manufacturing',
                'skills' => ['Tailoring', 'Pattern Making', 'Fabric Knowledge', 'Quality Control'],
                'benefits' => ['Piece Rate Bonus', 'Festival Bonus', 'Skill Development'],
                'employment_type' => 'full_time',
                'experience_level' => 'mid_level'
            ],
            [
                'title' => 'Event Coordinator',
                'description' => 'Dynamic event coordinator required for managing weddings and cultural events. Must have excellent organizational skills.',
                'requirements' => 'Graduate with event management experience. Excellent communication skills. Ability to work under pressure and flexible hours.',
                'category' => 'Event Management',
                'skills' => ['Event Planning', 'Coordination', 'Communication', 'Time Management'],
                'benefits' => ['Performance Bonus', 'Travel Allowance', 'Networking Opportunities'],
                'employment_type' => 'full_time',
                'experience_level' => 'mid_level'
            ],
            [
                'title' => 'Technician',
                'description' => 'Electronics repair technician needed for our service center. Must be skilled in repairing various electronic appliances.',
                'requirements' => 'ITI/Diploma in Electronics. 2+ years repair experience. Knowledge of modern electronic appliances and tools.',
                'category' => 'Technical Services',
                'skills' => ['Electronics Repair', 'Troubleshooting', 'Customer Service', 'Technical Knowledge'],
                'benefits' => ['Health Insurance', 'Tool Allowance', 'Training Programs'],
                'employment_type' => 'full_time',
                'experience_level' => 'mid_level'
            ]
        ];
        
        // Create job postings
        foreach ($jobTemplates as $index => $template) {
            $business = $businesses->get($index % $businesses->count());
            
            // Determine if job is active or expired
            $isActive = $faker->boolean(80); // 80% chance of being active
            $expiresAt = $isActive ? 
                Carbon::now()->addDays($faker->numberBetween(15, 90)) : 
                Carbon::now()->subDays($faker->numberBetween(1, 30));
            
            $applicationDeadline = $isActive ? 
                Carbon::now()->addDays($faker->numberBetween(10, 60)) : 
                Carbon::now()->subDays($faker->numberBetween(1, 15));
            
            JobPosting::create([
                'business_id' => $business->id,
                'title' => $template['title'],
                'description' => $template['description'],
                'requirements' => $template['requirements'],
                'salary_range' => $this->generateSalaryRange($template['experience_level']),
                'job_type' => $faker->randomElement(['permanent', 'temporary', 'contract']),
                'location' => $faker->randomElement($indianCities),
                'experience_level' => $template['experience_level'],
                'employment_type' => $template['employment_type'],
                'category' => $template['category'],
                'skills_required' => json_encode($template['skills']),
                'benefits' => json_encode($template['benefits']),
                'application_deadline' => $applicationDeadline,
                'is_active' => $isActive,
                'expires_at' => $expiresAt,
                'status' => $faker->randomElement(['approved', 'pending', 'approved', 'approved']) // More approved
            ]);
        }
        
        $this->command->info('10 job listings created successfully!');
    }
    
    /**
     * Generate salary range based on experience level
     */
    private function generateSalaryRange($experienceLevel): string
    {
        switch ($experienceLevel) {
            case 'entry_level':
                return '₹15,000 - ₹25,000 per month';
            case 'mid_level':
                return '₹25,000 - ₹45,000 per month';
            case 'senior_level':
                return '₹45,000 - ₹75,000 per month';
            default:
                return '₹20,000 - ₹35,000 per month';
        }
    }
}