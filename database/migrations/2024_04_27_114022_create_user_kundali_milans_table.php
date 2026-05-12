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
        Schema::create('user_kundali_milans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('device_id');
            $table->string('male_name');
            $table->date('male_dob');
            $table->time('male_time');
            $table->string('male_country',50);
            $table->string('male_city');
            $table->string('male_latitude');
            $table->string('male_longitude');
            $table->double('male_timezone');
            $table->string('female_name');
            $table->date('female_dob');
            $table->time('female_time');
            $table->string('female_country',50);
            $table->string('female_city');
            $table->string('female_latitude');
            $table->string('female_longitude');
            $table->double('female_timezone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_kundali_milans');
    }
};
