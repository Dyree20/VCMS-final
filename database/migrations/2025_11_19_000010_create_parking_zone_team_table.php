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
        Schema::create('parking_zone_team', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parking_zone_id');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            // Foreign keys
            $table->foreign('parking_zone_id')->references('id')->on('parking_zones')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');

            // Unique constraint so a zone can only be assigned once per team
            $table->unique(['parking_zone_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_zone_team');
    }
};
