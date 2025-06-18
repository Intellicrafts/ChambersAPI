<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lawyer>
 */
class LawyerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specializations = [
            'Criminal Law', 'Family Law', 'Corporate Law', 'Intellectual Property', 
            'Real Estate Law', 'Tax Law', 'Immigration Law', 'Labor Law', 
            'Environmental Law', 'Constitutional Law', 'Civil Rights Law'
        ];
        
        return [
            'id' => fake()->uuid(),
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'password_hash' => bcrypt('password'),
            'active' => fake()->boolean(90),
            'is_verified' => fake()->boolean(70),
            'license_number' => fake()->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'bar_association' => fake()->randomElement(['State Bar Association', 'American Bar Association', 'County Bar Association']),
            'specialization' => fake()->randomElement($specializations),
            'years_of_experience' => fake()->numberBetween(1, 30),
            'bio' => fake()->paragraph(3),
            'profile_picture_url' => null,
            'consultation_fee' => fake()->randomFloat(2, 50, 500),
            'deleted' => false,
        ];
    }
}
