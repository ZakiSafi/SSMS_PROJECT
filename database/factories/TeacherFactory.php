<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = [1404, 1405, 1406, 1407, 1408, 1409, 1410];
        return [

            'university_id' => $this->faker->numberBetween(1,30),
            'academic_year' => $this->faker->randomElement($year),
            'total_teachers' => $this->faker->numberBetween(50, 500),

        ];
    }
}
