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
        $year = [1404, 1405,1406, 1407];
        return [
            'academic_year' => $this->faker->randomElement($year),
            'university_id' => $this->faker->numberBetween(1, 15),
            'faculty_id' => $this->faker->numberBetween(1, 15),
            'department_id' => $this->faker->numberBetween(1, 10),
            'classroom' => $this->faker->numberBetween(1, 4),
            'semester_number' => $this->faker->numberBetween(1, 12),
            'shift' => $this->faker->randomElement(['day', 'night']),
            'season' => $this->faker->randomElement(['spring', 'autumn', 'summer', 'winter']),
            'male_total' => $this->faker->numberBetween(30, 100),
            'female_total' => $this->faker->numberBetween(0, 100),
            'student_type' => $this->faker->randomElement(['new', 'current', 'graduated']),

        ];
    }
}
