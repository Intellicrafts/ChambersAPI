<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
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
            'full_name' => fake()->name(),
            'email_address' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'company' => fake()->optional(0.7)->company(),
            'service_interested' => fake()->randomElement(['Legal Consultation', 'Document Review', 'Court Representation', 'Legal Advice', 'Contract Drafting']),
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(3),
            'status' => fake()->randomElement(['new', 'pending', 'in_progress', 'resolved', 'closed']),
        ];
    }
}