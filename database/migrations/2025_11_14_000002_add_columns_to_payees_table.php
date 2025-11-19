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
        Schema::table('payees', function (Blueprint $table) {
            // Add clamping_id if it doesn't exist
            if (!Schema::hasColumn('payees', 'clamping_id')) {
                $table->foreignId('clamping_id')->nullable()->constrained('clampings')->onDelete('cascade')->after('id');
            }
            
            // Add amount if it doesn't exist
            if (!Schema::hasColumn('payees', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->after('amount_paid');
            }
            
            // Add status if it doesn't exist
            if (!Schema::hasColumn('payees', 'status')) {
                $table->string('status')->default('pending')->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payees', function (Blueprint $table) {
            if (Schema::hasColumn('payees', 'clamping_id')) {
                $table->dropForeign(['clamping_id']);
                $table->dropColumn('clamping_id');
            }
            if (Schema::hasColumn('payees', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('payees', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
