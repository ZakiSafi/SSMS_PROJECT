<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentStatistic>
 */
class StudentStatisticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => $this->faker->numberBetween(1, 10),
            'university_id' => $this->faker->numberBetween(1, 20),
           'faculty_id' => $this->faker->numberBetween(1, 15),
            'department_id' => $this->faker->numberBetween(1, 10),
            'classroom_id' => $this->faker->numberBetween(1,4),
            'male_total' => $this->faker->numberBetween(0, 100),
            'female_total' => $this->faker->numberBetween(0, 100),
            'student_type' => $this->faker->randomElement(['new', 'current', 'graduated']),

        ];
    }
}
