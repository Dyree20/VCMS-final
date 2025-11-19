<?php

namespace Database\Seeders;

use App\Models\ParkingZone;
use App\Models\User;
use Illuminate\Database\Seeder;

class ParkingZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user (created_by)
        $admin = User::where('role_id', 1)->first() ?? User::first();
        $adminId = $admin?->id ?? 1;

        $zones = [
            [
                'name' => 'Downtown Zone A',
                'description' => 'Main downtown parking zone with high traffic',
                'latitude' => 10.3157,
                'longitude' => 123.8854,
                'radius_meters' => 500,
                'fine_amount' => 500.00,
                'status' => 'active',
                'created_by' => $adminId,
            ],
            [
                'name' => 'Waterfront District',
                'description' => 'Coastal area parking zone',
                'latitude' => 10.2868,
                'longitude' => 123.9125,
                'radius_meters' => 750,
                'fine_amount' => 750.00,
                'status' => 'active',
                'created_by' => $adminId,
            ],
            [
                'name' => 'Business Hub Zone',
                'description' => 'Commercial district with restricted parking',
                'latitude' => 10.3228,
                'longitude' => 123.8725,
                'radius_meters' => 600,
                'fine_amount' => 600.00,
                'status' => 'active',
                'created_by' => $adminId,
            ],
            [
                'name' => 'Residential Area B',
                'description' => 'Residential district zone (currently under maintenance)',
                'latitude' => 10.3100,
                'longitude' => 123.8600,
                'radius_meters' => 400,
                'fine_amount' => 300.00,
                'status' => 'maintenance',
                'created_by' => $adminId,
            ],
        ];

        foreach ($zones as $zone) {
            ParkingZone::firstOrCreate(
                ['name' => $zone['name']],
                $zone
            );
        }
    }
}
