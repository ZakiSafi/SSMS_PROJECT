<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Contracts\Role as ContractsRole;
use Spatie\Permission\Models\Role as ModelsRole;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Role
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Do Anything'
        ]);

        // Define permissions
        $permissions = [
            'faculties.view',
            'faculties.create',
            'faculties.edit',
            'faculties.delete',

            'provinces.view',
            'provinces.create',
            'provinces.edit',
            'provinces.delete',

            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',

            'university.view',
            'university.create',
            'university.edit',
            'university.delete',

            'teachers.view',
            'teachers.create',
            'teachers.edit',
            'teachers.delete',

            'student_statistic.view',
            'student_statistic.create',
            'student_statistic.edit',
            'student_statistic.delete',

            'current_students.view',
            'current_students.create',
            'current_students.edit',
            'current_students.delete',

            'graduated_students.view',
            'graduated_students.create',
            'graduated_students.edit',
            'graduated_students.delete',

            'settings.view',
            'settings.create',
            'settings.edit',
            'settings.delete',

            'dashboard.view'
        ];


        // Create permissions and assign them to the Admin role
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($permission);
        }

        // Create Admin User
        User::create([
            'name' => "Zaki",
            'email' => "safi@gmail.com",
            'university_id' => null, // Assuming no university is assigned
            'password' => bcrypt('12345678'),
            'role_id' => 1, // Assign the role ID to the user
        ])->assignRole($adminRole); // Assign the Admin role to the user
    }
}