<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use App\Models\BusinessCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'business_name' => fake()->company(),
            'business_type' => fake()->randomElement(['Agriculture', 'Food & Beverages', 'Retail', 'Services', 'Manufacturing']),
            'category_id' => BusinessCategory::factory(),
            'description' => fake()->paragraph(),
            'contact_phone' => '+91-' . fake()->numerify('##########'),
            'contact_email' => fake()->companyEmail(),
            'website' => fake()->url(),
            'verification_status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'subscription_status' => fake()->randomElement(['active', 'inactive', 'expired']),
            'subscription_expires_at' => fake()->dateTimeBetween('now', '+1 year'),
            'job_posting_limit' => fake()->numberBetween(5, 50),
            'verified_at' => fake()->boolean(70) ? now() : null,
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the business is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the business has an active subscription.
     */
    public function activeSubscription(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_status' => 'active',
            'subscription_expires_at' => fake()->dateTimeBetween('+1 month', '+1 year'),
        ]);
    }
}