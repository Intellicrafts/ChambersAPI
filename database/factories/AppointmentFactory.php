<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Lawyer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('+1 days', '+1 week');

        return [
            'id' => fake()->uuid(),
            'user_id' => User::factory(), // Will be overridden in the seeder
            'lawyer_id' => null, // Will be set in the seeder
            'appointment_time' => $startTime,
            'duration_minutes' => fake()->randomElement([30, 45, 60]),
            'status' => fake()->randomElement([
                Appointment::STATUS_SCHEDULED,
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_CANCELLED,
                Appointment::STATUS_NO_SHOW,
                Appointment::STATUS_IN_PROGRESS,
            ]),
            'meeting_link' => fake()->url(),
        ];
    }
}
