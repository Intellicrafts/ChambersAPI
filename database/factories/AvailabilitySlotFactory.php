<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AvailabilitySlot>
 */
class AvailabilitySlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a random start time in the next 14 days
        $startTime = now()->addDays(rand(1, 14))->setHour(rand(9, 16))->setMinute(0)->setSecond(0);
        
        // End time is 1 hour after start time
        $endTime = clone $startTime;
        $endTime->addHour();
        
        return [
            'id' => fake()->uuid(),
            'lawyer_id' => null, // This will be set in the seeder
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_booked' => fake()->boolean(20), // 20% chance of being booked
        ];
    }
}
