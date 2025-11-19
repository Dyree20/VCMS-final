<?php

use App\Models\ParkingZone;

require __DIR__ . '/bootstrap/app.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check active zones
$activeZones = ParkingZone::where('status', 'active')->get();

echo "Active Parking Zones:\n";
echo "=====================\n";

if ($activeZones->count() > 0) {
    foreach ($activeZones as $zone) {
        echo "\n✓ Zone: " . $zone->name . "\n";
        echo "  ID: " . $zone->id . "\n";
        echo "  Status: " . $zone->status . "\n";
        echo "  Fine: ₱" . number_format($zone->fine_amount, 2) . "\n";
        echo "  Radius: " . number_format($zone->radius_meters) . "m\n";
        echo "  Location: (" . $zone->latitude . ", " . $zone->longitude . ")\n";
    }
} else {
    echo "\n❌ No active parking zones found.\n";
    echo "Please create at least one active parking zone.\n";
}

echo "\n\nAll Parking Zones:\n";
echo "==================\n";

$allZones = ParkingZone::all();
foreach ($allZones as $zone) {
    echo "\n- " . $zone->name . " [" . strtoupper($zone->status) . "]\n";
}
