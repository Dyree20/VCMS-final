<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN easily
            DB::statement('PRAGMA foreign_keys=OFF;');
        } else {
            Schema::table('archives', function (Blueprint $table) {
                $table->string('vehicle_type')->nullable()->change();
            });
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');
        } else {
            Schema::table('archives', function (Blueprint $table) {
                $table->string('vehicle_type')->nullable(false)->change();
            });
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }
};


