<?php

namespace Database\Factories;

use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faculty>
 */
class FacultyFactory extends Factory
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
                'Faculty of Science',
                'Faculty of Arts',
                'Faculty of Engineering',
                'Faculty of Medicine',
                'Faculty of Law',
                'Faculty of Business',
                'Faculty of Education',
                'Faculty of Social Sciences',
                'Faculty of Humanities',
                'Faculty of Agriculture',
                'Faculty of Architecture',
                'Faculty of computer Science',
                'Faculty of sharia',
                'Faculty of pharmacy',
                'Faculty of social health',
            ]),
            'university_id' => University::inRandomOrder()->first()->id ?? University::factory()->create()->id,
        ];
    }
}
