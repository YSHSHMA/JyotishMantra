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
        Schema::create('trust_pandit_transections', function (Blueprint $table) {
            $table->id();
            $table->text('order_id')->nullable();   
            $table->unsignedBigInteger('temple_id');
            $table->Integer('trust_id');
            $table->Integer('pandit_id')->nullable();   
            $table->Integer('package_id')->nullable();
            $table->string('package_price');
            $table->string('payment_method')->nullable();
            $table->text('payment_status')->nullable();   
            $table->Integer('status')->default(1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trust_pandit_transections');
    }
};
