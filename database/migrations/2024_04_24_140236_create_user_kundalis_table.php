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
        Schema::create('user_kundalis', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('device_id');
            $table->string('name');
            $table->date('dob');
            $table->time('time');
            $table->string('country',50);
            $table->string('city');
            $table->string('latitude');
            $table->string('longitude');
            $table->double('timezone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_kundalis');
    }
};
