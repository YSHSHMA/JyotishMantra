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
        
        Schema::create('pandit_price_slabs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pandit_id');
            $table->unsignedBigInteger('service_id');
    
            $table->integer('min_qty');
            $table->integer('max_qty');
    
            $table->decimal('price', 10, 2);
            $table->decimal('single_price', 10, 2)->nullable();
    
            $table->string('type', 50);
            // status column
            $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pandit_price_slabs');
    }
};
