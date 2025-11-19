<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\UserStatus;

class UserStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use firstOrCreate to avoid duplicate key errors
        \App\Models\UserStatus::firstOrCreate(
            ['status' => 'Pending'],
            ['description' => 'User awaiting admin approval before accessing the system']
        );
        \App\Models\UserStatus::firstOrCreate(
            ['status' => 'Approved'],
            ['description' => 'User has been approved and can access the system']
        );
        \App\Models\UserStatus::firstOrCreate(
            ['status' => 'Suspended'],
            ['description' => 'User is temporarily suspended due to violation or admin decision']
        );
        \App\Models\UserStatus::firstOrCreate(
            ['status' => 'Rejected'],
            ['description' => 'User registration was rejected by admin']
        );
    }
}
