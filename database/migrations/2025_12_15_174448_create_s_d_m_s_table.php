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
        Schema::create('s_d_m_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collector_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->bigInteger('mobile');
            $table->string('password');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('collector_id')->references('id')->on('collectors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_d_m_s');
    }
};
