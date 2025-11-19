<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use firstOrCreate to avoid duplicate key errors
        \App\Models\Role::firstOrCreate(['name' => 'Admin']);
        \App\Models\Role::firstOrCreate(['name' => 'Enforcer']);
        \App\Models\Role::firstOrCreate(['name' => 'Front Desk']);
    }
}
