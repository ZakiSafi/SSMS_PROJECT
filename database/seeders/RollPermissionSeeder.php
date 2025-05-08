<?php

namespace Database\Seeders;

use App\Models\RollPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RollPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RollPermission::factory()->count(4)->create();
    }
}
