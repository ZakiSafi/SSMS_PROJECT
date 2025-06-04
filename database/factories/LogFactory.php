<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Log>
 */
class LogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'university_id' => $this->faker->randomElement([1]), // Assuming universities with IDs 1, 2, and 3 exist
            'action_type' => $this->faker->randomElement(['create', 'update', 'delete']),
            'action_description' => $this->faker->sentence(),
            'table_name' => $this->faker->randomElement(['users', 'provinces', 'universities']),
            'record_id' => $this->faker->numberBetween(1, 100),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),

        ];
    }
}
