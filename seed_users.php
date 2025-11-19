<?php
/**
 * Quick script to seed test users into the database
 * Run: php seed_users.php
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\UserStatus;
use Illuminate\Support\Facades\Hash;

echo "Seeding test users...\n";

$adminRole = Role::where('name', 'Admin')->first();
$enforcerRole = Role::where('name', 'Enforcer')->first();
$approvedStatus = UserStatus::where('status', 'Approved')->first();

if (!$adminRole) {
    echo "❌ Admin role not found\n";
    exit(1);
}
if (!$enforcerRole) {
    echo "❌ Enforcer role not found\n";
    exit(1);
}
if (!$approvedStatus) {
    echo "❌ Approved status not found\n";
    exit(1);
}

// Create admin user
$admin = User::firstOrCreate(
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
echo "✅ Admin user (email: admin@example.com, password: admin123)\n";

// Create enforcer user
$enforcer = User::firstOrCreate(
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
echo "✅ Enforcer user (email: enforcer1@example.com, password: enforcer123)\n";

echo "\n✅ Test users seeded successfully!\n";
echo "\nYou can now login with:\n";
echo "  Admin:    admin@example.com / admin123\n";
echo "  Enforcer: enforcer1@example.com / enforcer123\n";
