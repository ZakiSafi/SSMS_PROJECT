<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'user', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}