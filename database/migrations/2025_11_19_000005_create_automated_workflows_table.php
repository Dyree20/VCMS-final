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
        Schema::create('automated_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['auto_assign', 'payment_reminder', 'auto_archive', 'escalation'])->default('auto_assign');
            $table->json('conditions')->nullable();
            $table->json('actions')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->integer('execution_order')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->index('type');
            $table->index('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automated_workflows');
    }
};
