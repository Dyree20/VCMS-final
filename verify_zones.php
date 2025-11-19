<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$zones = DB::table('parking_zones')->get(['name', 'status', 'fine_amount']);

echo "\n✅ Active Parking Zones Created:\n";
echo "================================\n\n";

foreach ($zones as $zone) {
    $badge = $zone->status === 'active' ? '✓ ACTIVE' : '⚠ ' . strtoupper($zone->status);
    echo "• {$zone->name}\n";
    echo "  Status: {$badge}\n";
    echo "  Fine: ₱" . number_format($zone->fine_amount, 2) . "\n\n";
}

echo "Total zones: " . count($zones) . "\n";
