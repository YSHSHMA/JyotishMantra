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
        Schema::create('purohits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('temple_id');
            $table->string('name');
            $table->string('mobile');
            $table->string('profile')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();   
            $table->Integer('status')->default(1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purohits');
    }
};
