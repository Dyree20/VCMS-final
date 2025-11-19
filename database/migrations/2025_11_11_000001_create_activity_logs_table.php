<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: After adding this migration you must run `php artisan migrate` to create the table.
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('username')->nullable();
            $table->string('action');
            $table->unsignedBigInteger('clamping_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
