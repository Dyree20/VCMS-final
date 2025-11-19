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
        Schema::create('payees', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no');
            $table->string('name');
            $table->string('contact_number')->nullable();
            $table->enum('payment_method', ['walk-in', 'online']);
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamps();

            $table->foreign('ticket_no')->references('ticket_no')->on('clampings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payees');
    }
};
