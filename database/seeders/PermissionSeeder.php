<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'dashboard.view',
            'teachers.view',
            'teachers.create',
            'teachers.edit',
            'teachers.delete',
            // Add ALL permissions from your frontend
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Create super-admin role
        $admin = Role::create(['name' => 'super-admin']);
        $admin->givePermissionTo(Permission::all());
    }
}
