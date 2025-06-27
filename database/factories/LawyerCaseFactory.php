<?php

namespace Database\Factories;

use App\Models\LawyerCase;
use App\Models\User;
use App\Models\Lawyer;
use App\Models\LawyerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LawyerCase>
 */
class LawyerCaseFactory extends Factory
{
    protected $model = LawyerCase::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'lawyer_id' => Lawyer::inRandomOrder()->first()->id,
            'casename' => $this->faker->sentence(3),
            'category_id' => LawyerCategory::inRandomOrder()->first()->id,
        ];
    }
}