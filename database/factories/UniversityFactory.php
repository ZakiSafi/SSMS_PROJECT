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
        $afghanUniversities = [
            "Kabul University",
            "Kabul Polytechnic University",
            "Kabul Medical University",
            "Kabul Education University",
            "Kabul University of Science and Technology",
            "Herat University",
            "Nangarhar University",
            "Balkh University",
            "Mazar-i-Sharif University",
            "Kandahar University",
            "Badakhshan University",
            "Ghazni University",
            "Bamyan University",
            "Takhar University",
            "Laghman University",
            "Khost University",
            "Parwan University",
            "Paktia University",
            "Panjshir University",
            "Kunar University",
            "Samangan University",
            "Faryab University",
            "Jawzjan University",
            "Wardak University",
            "Kapisa University",
            "Nuristan University",
            "Logar University",
            "Daikundi University",
            "Farah University",
            "Urozgan University"
          ];

            return [
            'name' => fake()->unique()->randomElement($afghanUniversities),
            'type' => fake()->randomElement(['public', 'private']),
            'province_id' => Province::inRandomOrder()->first()->id ?? Province::factory()->create()->id,
        ];
    }
}
