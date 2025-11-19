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
        Schema::create('device_managers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('device_name'); // e.g., "Chrome on Windows", "Safari on iPhone"
            $table->string('ip_address');
            $table->string('browser')->nullable(); // Chrome, Firefox, Safari, etc.
            $table->string('os')->nullable(); // Windows, macOS, Linux, iOS, Android
            $table->string('device_type')->nullable(); // Desktop, Tablet, Mobile
            $table->string('user_agent')->nullable(); // Full user agent string
            $table->timestamp('last_activity_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('session_token')->nullable()->unique(); // Token to identify this session
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('session_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_managers');
    }
};
