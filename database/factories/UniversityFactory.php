<?php

namespace Database\Factories;

use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\University>
 */
class UniversityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'teachers' => fake()->numberBetween(1, 100),
            'type' => fake()->randomElement(['public', 'private']),
            'province_id' => Province::inRandomOrder()->first()->id ?? Province::factory()->create()->id,
        ];
    }
}
