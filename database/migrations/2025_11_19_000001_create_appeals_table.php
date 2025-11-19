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
        Schema::create('appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clamping_id')->constrained('clampings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->string('reason');
            $table->text('description');
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['clamping_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appeals');
    }
};
