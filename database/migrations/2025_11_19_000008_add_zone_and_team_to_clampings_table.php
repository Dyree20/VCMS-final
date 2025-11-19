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
        Schema::table('clampings', function (Blueprint $table) {
            if (!Schema::hasColumn('clampings', 'parking_zone_id')) {
                $table->foreignId('parking_zone_id')->nullable()->constrained('parking_zones')->onDelete('set null');
            }
            if (!Schema::hasColumn('clampings', 'team_id')) {
                $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clampings', function (Blueprint $table) {
            if (Schema::hasColumn('clampings', 'parking_zone_id')) {
                $table->dropForeign(['parking_zone_id']);
                $table->dropColumn('parking_zone_id');
            }
            if (Schema::hasColumn('clampings', 'team_id')) {
                $table->dropForeign(['team_id']);
                $table->dropColumn('team_id');
            }
        });
    }
};
