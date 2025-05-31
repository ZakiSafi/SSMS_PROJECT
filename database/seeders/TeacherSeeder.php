<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Teacher::factory(10)->create([
            'university_id' => \App\Models\University::factory(),
            'academic_year' => '1404',
            'total_teachers' => 100,
        ]);

        \App\Models\Teacher::factory(10)->create([
            'university_id' => \App\Models\University::factory(),
            'academic_year' => '1405',
            'total_teachers' => 200,
        ]);
        \App\Models\Teacher::factory(10)->create([
            'university_id' => \App\Models\University::factory(),
            'academic_year' => '1406',
            'total_teachers' => 300,
        ]);
    }
}
