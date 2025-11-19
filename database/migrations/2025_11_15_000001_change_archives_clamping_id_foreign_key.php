<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing foreign key constraint
        Schema::table('archives', function (Blueprint $table) {
            $table->dropForeign(['clamping_id']);
        });

        // Make clamping_id nullable to support set null
        // Note: This works for MySQL/PostgreSQL. For SQLite, you may need to recreate the table
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN easily
            // We'll use a raw SQL approach
            DB::statement('PRAGMA foreign_keys=OFF;');
            // Note: For SQLite, the column modification might require table recreation
            // This is a limitation - consider using MySQL/PostgreSQL for production
        } else {
            Schema::table('archives', function (Blueprint $table) {
                $table->unsignedBigInteger('clamping_id')->nullable()->change();
            });
        }

        // Re-add the foreign key with set null instead of cascade
        // This allows archives to persist even if the clamping is deleted
        Schema::table('archives', function (Blueprint $table) {
            $table->foreign('clamping_id')
                ->references('id')
                ->on('clampings')
                ->onDelete('set null');
        });

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        Schema::table('archives', function (Blueprint $table) {
            // Drop the set null foreign key
            $table->dropForeign(['clamping_id']);
        });

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');
        } else {
            Schema::table('archives', function (Blueprint $table) {
                // Make clamping_id not nullable again
                $table->unsignedBigInteger('clamping_id')->nullable(false)->change();
            });
        }

        Schema::table('archives', function (Blueprint $table) {
            // Restore the cascade foreign key
            $table->foreign('clamping_id')
                ->references('id')
                ->on('clampings')
                ->onDelete('cascade');
        });

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }
};

