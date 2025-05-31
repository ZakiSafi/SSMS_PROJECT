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

            'university_id' => \App\Models\University::factory(),
            'academic_year' => $this->faker->randomElement($year),
            'total_teachers' => $this->faker->numberBetween(50, 500),

        ];
    }
}
