<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusinessCategory>
 */
class BusinessCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Agriculture & Farming' => 'Agricultural products and farming services',
            'Food & Beverages' => 'Food products and catering services',
            'Retail & Trading' => 'Retail shops and trading businesses',
            'Services' => 'Professional and personal services',
            'Manufacturing' => 'Manufacturing and production businesses',
            'Transportation' => 'Transportation and logistics services',
            'Healthcare' => 'Healthcare and medical services',
            'Education' => 'Educational services and institutions',
            'Technology' => 'IT and technology services',
            'Construction' => 'Construction and real estate services'
        ];
        
        $category = fake()->randomElement(array_keys($categories));
        
        return [
            'name' => $category,
            'description' => $categories[$category],
            'status' => 'active',
        ];
    }
}