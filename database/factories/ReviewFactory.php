<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comments = [
            'Excellent service and very knowledgeable.',
            'Helped me understand my legal options clearly.',
            'Very professional and responsive.',
            'Took the time to explain complex legal matters in simple terms.',
            'Highly recommend for their expertise.',
            'Resolved my case efficiently.',
            'Great communication throughout the process.',
            'Very thorough and detail-oriented.',
            'Exceeded my expectations.',
            'Would definitely use their services again.',
            'Prompt responses to all my questions.',
            'Made me feel comfortable during a stressful situation.',
            'Extremely competent and trustworthy.',
            'Fair pricing for the quality of service provided.',
            'Helped me achieve a favorable outcome.'
        ];
        
        return [
            'id' => fake()->uuid(),
            'user_id' => null, // Will be set in the seeder
            'lawyer_id' => null, // Will be set in the seeder
            'rating' => fake()->numberBetween(3, 5), // Mostly positive ratings
            'comment' => fake()->randomElement($comments),
        ];
    }
}
