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
        static $faculties = [
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
            'Faculty of Computer Science',
            'Faculty of Sharia',
            'Faculty of Pharmacy',
            'Faculty of Social Health',
        ];

        static $index = 0;

        if ($index >= count($faculties)) {
            throw new \Exception('No more unique faculties available.');
        }

        return [
            'name' => $faculties[$index++],
        ];
    }
}
