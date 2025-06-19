<?php

namespace Database\Factories;
use App\Models\LawyersCase;
use App\Models\User;
use App\Models\Lawyer;
use App\Models\LawyerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LawyersCase>
 */
class LawyersCaseFactory extends Factory
{
    protected $model = LawyersCase::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'lawyer_id' => Lawyer::factory(),
            'casename' => $this->faker->sentence(3),
            'category_id' => LawyerCategory::factory(),
        ];
    }
}
