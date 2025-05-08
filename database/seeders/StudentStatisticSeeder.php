<?php

namespace Database\Seeders;

use App\Models\StudentStatistic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentStatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudentStatistic::factory(30)->create();
    }
}
