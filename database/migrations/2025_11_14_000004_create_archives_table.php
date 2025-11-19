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
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clamping_id')->constrained('clampings')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ticket_no');
            $table->string('plate_no');
            $table->string('vehicle_type');
            $table->text('reason');
            $table->string('location');
            $table->decimal('fine_amount', 10, 2);
            $table->enum('archived_status', ['released', 'cancelled'])->default('released');
            $table->timestamp('archived_date')->useCurrent();
            $table->foreignId('archived_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for faster queries
            $table->index('user_id');
            $table->index('archived_status');
            $table->index('archived_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
