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
        Schema::create('s_d_m_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sdm_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->bigInteger('mobile');
            $table->string('password');
            $table->json('temples');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('sdm_id')->references('id')->on('s_d_m_s')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_d_m_employees');
    }
};
