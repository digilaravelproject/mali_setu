<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\BusinessLocation;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_IN');
        
        // Get business users
        $businessUsers = User::where('user_type', 'business')->get();
        
        // Create business categories if they don't exist
        $categories = [
            ['name' => 'Agriculture & Farming', 'description' => 'Agricultural products and farming services'],
            ['name' => 'Food & Beverages', 'description' => 'Food products and catering services'],
            ['name' => 'Retail & Trading', 'description' => 'Retail shops and trading businesses'],
            ['name' => 'Services', 'description' => 'Professional and personal services'],
            ['name' => 'Manufacturing', 'description' => 'Manufacturing and production businesses'],
        ];
        
        foreach ($categories as $category) {
            BusinessCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
        
        $businessCategories = BusinessCategory::all();
        $indianCities = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata', 'Hyderabad', 'Pune', 'Ahmedabad', 'Jaipur', 'Lucknow'];
        
        // Business data templates
        $businessTemplates = [
            [
                'name' => 'Mali Fresh Vegetables',
                'type' => 'product',
                'category' => 'Agriculture',
                'description' => 'Fresh organic vegetables directly from farm to your table. We specialize in seasonal vegetables and maintain highest quality standards.',
                'products' => ['Tomatoes', 'Onions', 'Potatoes', 'Leafy Greens', 'Seasonal Vegetables'],
                'services' => ['Home Delivery', 'Bulk Supply', 'Organic Certification']
            ],
            [
                'name' => 'Sai Catering Services',
                'type' => 'service',
                'category' => 'Food & Beverages',
                'description' => 'Traditional Maharashtrian cuisine catering for weddings, events, and corporate functions.',
                'products' => ['Wedding Packages', 'Corporate Lunch', 'Traditional Thali', 'Sweets & Snacks'],
                'services' => ['Event Catering', 'Home Delivery', 'Custom Menu Planning']
            ],
            [
                'name' => 'Ganesh General Store',
                'type' => 'product',
                'category' => 'Retail',
                'description' => 'Your neighborhood store for daily essentials, groceries, and household items.',
                'products' => ['Groceries', 'Household Items', 'Personal Care', 'Stationery'],
                'services' => ['Home Delivery', 'Credit Facility', 'Online Ordering']
            ],
            [
                'name' => 'Shivaji Transport Services',
                'type' => 'service',
                'category' => 'Logistics',
                'description' => 'Reliable transportation services for goods and passengers across Maharashtra.',
                'products' => ['Truck Rental', 'Tempo Services', 'Passenger Transport'],
                'services' => ['Goods Transportation', 'Packers & Movers', 'Logistics Solutions']
            ],
            [
                'name' => 'Laxmi Textiles',
                'type' => 'product',
                'category' => 'Manufacturing',
                'description' => 'Quality textile manufacturing with traditional and modern designs.',
                'products' => ['Cotton Fabrics', 'Silk Sarees', 'Dress Materials', 'Home Textiles'],
                'services' => ['Custom Tailoring', 'Bulk Orders', 'Design Consultation']
            ],
            [
                'name' => 'Marathi Mandal Event Management',
                'type' => 'service',
                'category' => 'Services',
                'description' => 'Complete event management services for weddings, cultural programs, and corporate events.',
                'products' => ['Decoration Packages', 'Sound Systems', 'Photography Services'],
                'services' => ['Event Planning', 'Venue Booking', 'Artist Management']
            ],
            [
                'name' => 'Krishna Dairy Products',
                'type' => 'product',
                'category' => 'Food & Beverages',
                'description' => 'Fresh dairy products from our own dairy farm with home delivery services.',
                'products' => ['Fresh Milk', 'Paneer', 'Ghee', 'Yogurt', 'Butter'],
                'services' => ['Daily Delivery', 'Subscription Plans', 'Bulk Supply']
            ],
            [
                'name' => 'Bharat Electronics Repair',
                'type' => 'service',
                'category' => 'Services',
                'description' => 'Expert repair services for all electronic appliances and gadgets.',
                'products' => ['Spare Parts', 'Refurbished Electronics'],
                'services' => ['TV Repair', 'Mobile Repair', 'Home Appliance Service', 'Warranty Service']
            ],
            [
                'name' => 'Pune Agro Solutions',
                'type' => 'product',
                'category' => 'Agriculture',
                'description' => 'Modern farming solutions, seeds, fertilizers, and agricultural equipment.',
                'products' => ['Seeds', 'Fertilizers', 'Pesticides', 'Farm Tools'],
                'services' => ['Soil Testing', 'Crop Consultation', 'Equipment Rental']
            ],
            [
                'name' => 'Maharaja Sweets & Snacks',
                'type' => 'product',
                'category' => 'Food & Beverages',
                'description' => 'Traditional Maharashtrian sweets and snacks made with authentic recipes.',
                'products' => ['Modak', 'Puran Poli', 'Chakli', 'Laddu', 'Namkeen'],
                'services' => ['Custom Orders', 'Festival Specials', 'Bulk Supply']
            ]
        ];
        
        // Create businesses
        foreach ($businessTemplates as $index => $template) {
            $user = $businessUsers->get($index % $businessUsers->count());
            $category = $businessCategories->random();
            
            $business = Business::create([
                'user_id' => $user->id,
                'business_name' => $template['name'],
                'business_type' => $template['type'],
                'category_id' => $category->id,
                'description' => $template['description'],
                'contact_phone' => '+91-' . $faker->numerify('##########'),
                'contact_email' => strtolower(str_replace(' ', '', $template['name'])) . '@business.com',
                'website' => 'www.' . strtolower(str_replace(' ', '', $template['name'])) . '.com',
                'verification_status' => $faker->randomElement(['approved', 'pending', 'approved', 'approved']), // More approved
                'subscription_status' => $faker->randomElement(['active', 'expired', 'trial', 'active']),
                'subscription_expires_at' => now()->addMonths($faker->numberBetween(1, 12)),
                'job_posting_limit' => $faker->numberBetween(5, 20),
                'verified_at' => $faker->boolean(80) ? now() : null,
                'status' => 'active',
            ]);
            
            // Create business location
            BusinessLocation::create([
                'business_id' => $business->id,
                'address_line_1' => $faker->streetAddress,
                'address_line_2' => $faker->optional()->streetSuffix,
                'city' => $faker->randomElement($indianCities),
                'state' => 'Maharashtra',
                'postal_code' => $faker->numerify('######'),
                'country' => 'India',
                'latitude' => $faker->latitude(18.5, 19.5),
                'longitude' => $faker->longitude(72.8, 73.8),
                'is_primary' => true,
            ]);
            
            // Create products
            foreach ($template['products'] as $productName) {
                Product::create([
                    'business_id' => $business->id,
                    'name' => $productName,
                    'description' => 'High quality ' . strtolower($productName) . ' available at competitive prices.',
                    'cost' => $faker->randomFloat(2, 50, 5000),
                    'image_path' => 'products/default-product.jpg',
                    'status' => 'active',
                ]);
            }
            
            // Create services
            foreach ($template['services'] as $serviceName) {
                Service::create([
                    'business_id' => $business->id,
                    'name' => $serviceName,
                    'description' => 'Professional ' . strtolower($serviceName) . ' service with experienced team.',
                    'cost' => $faker->randomFloat(2, 100, 10000),
                    'image_path' => 'services/default-service.jpg',
                    'status' => 'active',
                ]);
            }
        }
        
        $this->command->info('10 businesses with products and services created successfully!');
    }
}