<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LawyerCategory>
 */
class LawyerCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Criminal Law', 'Family Law', 'Corporate Law', 'Intellectual Property', 
            'Real Estate Law', 'Tax Law', 'Immigration Law', 'Labor Law', 
            'Environmental Law', 'Constitutional Law', 'Civil Rights Law',
            'Personal Injury', 'Medical Malpractice', 'Bankruptcy', 'Estate Planning'
        ];
        
        return [
            'id' => fake()->uuid(),
            'category_name' => fake()->unique()->randomElement($categories),
            'lawyer_id' => null, // Will be set in the seeder if needed
        ];
    }
}
