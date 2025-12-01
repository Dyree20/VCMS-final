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
        Schema::table('user_details', function (Blueprint $table) {
            if (!Schema::hasColumn('user_details', 'country')) {
                $table->string('country')->nullable()->after('address');
            }
            if (!Schema::hasColumn('user_details', 'city')) {
                $table->string('city')->nullable()->after('country');
            }
            if (!Schema::hasColumn('user_details', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('city');
            }
            if (!Schema::hasColumn('user_details', 'bio')) {
                $table->text('bio')->nullable()->after('postal_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            if (Schema::hasColumn('user_details', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('user_details', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('user_details', 'postal_code')) {
                $table->dropColumn('postal_code');
            }
            if (Schema::hasColumn('user_details', 'bio')) {
                $table->dropColumn('bio');
            }
        });
    }
};
