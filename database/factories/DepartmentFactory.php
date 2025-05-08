<?php

namespace Database\Factories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'IT',
                'SE',
                'Is',
                'CS',
                'CE',
                'EE',
                'ME',
                'Civil',
                'Architecture',
                'Business',
                'Law',
                'Medicine',
                'Finance',
                'Marketing',
                'Accounting',
                'Economics',
                'Psychology',
                'Sociology',
                'History',
                'Philosophy',
            ]),
            'faculty_id' => Faculty::inRandomOrder()->first()->id ?? Faculty::factory()->create()->id, // Assuming you have a Faculty factory and data seeded
        ];
    }
}
