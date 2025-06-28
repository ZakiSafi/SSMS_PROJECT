<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ProvinceSeeder;
use Database\Seeders\UniversitySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProvinceSeeder::class,
            UniversitySeeder::class,
            FacultySeeder::class,
            DepartmentSeeder::class,
            SettingSeeder::class,
            RollPermissionSeeder::class,
            UserSeeder::class,
            LogSeeder::class,
            TeacherSeeder::class,
        ]);
    }
}
