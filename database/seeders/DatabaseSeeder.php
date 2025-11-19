<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call your roles and statuses seeders first (only if not already seeded)
        // Skip if they already exist to avoid duplicate key errors
        $this->call([
            RolesTableSeeder::class,
            UserStatusesSeeder::class,
        ]);

        // Get the role and status IDs
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        $enforcerRole = \App\Models\Role::where('name', 'Enforcer')->first();
        $approvedStatus = \App\Models\UserStatus::where('status', 'Approved')->first();

        // Create a default Admin account if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'f_name' => 'System',
                'l_name' => 'Admin',
                'enforcer_id' => 'ENF-ADMIN-001',
                'username' => 'admin',
                'phone' => '09123456789',
                'role_id' => $adminRole->id,
                'status_id' => $approvedStatus->id,
                'password' => Hash::make('admin123'),
            ]
        );

        // Create a default Enforcer Account if it doesn't exist
        User::firstOrCreate(
            ['email' => 'enforcer1@example.com'],
            [
                'f_name' => 'Juan',
                'l_name' => 'Dela Cruz',
                'enforcer_id' => 'ENF-0001',
                'username' => 'enforcer1',
                'phone' => '09987654321',
                'role_id' => $enforcerRole->id,
                'status_id' => $approvedStatus->id,
                'password' => Hash::make('enforcer123'),
            ]
        );
        
    }
}
