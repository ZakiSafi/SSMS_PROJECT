<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ProvinceSeeder;
use Database\Seeders\UniversitySeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\FacultySeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\TeacherSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\LogSeeder;
use RolePermissionSeeder as GlobalRolePermissionSeeder;
use RoleSeeder as GlobalRoleSeeder;

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
            TeacherSeeder::class,
             AdminSeeder::class,
            LogSeeder::class,



        ]);
    }
}
