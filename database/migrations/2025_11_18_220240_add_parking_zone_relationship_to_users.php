<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add parking_zone_id column if it doesn't exist
            if (!Schema::hasColumn('users', 'parking_zone_id')) {
                $table->unsignedBigInteger('parking_zone_id')->nullable()->after('assigned_area');
                
                // Add foreign key constraint
                $table->foreign('parking_zone_id')
                    ->references('id')
                    ->on('parking_zones')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'parking_zone_id')) {
                try {
                    $table->dropForeign(['parking_zone_id']);
                } catch (\Exception $e) {
                    // Ignore if foreign key doesn't exist
                }
                $table->dropColumn('parking_zone_id');
            }
        });
    }
};
