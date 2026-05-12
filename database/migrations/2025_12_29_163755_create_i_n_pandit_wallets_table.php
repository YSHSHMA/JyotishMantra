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
        Schema::create('i_n_pandit_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pandit_id');
            $table->unsignedBigInteger('service_id');
            $table->string('type', 50);
            $table->decimal('amount', 10, 2);
            $table->decimal('cradit', 10, 2)->nullable();
            $table->decimal('debit', 10, 2)->nullable();
            $table->decimal('balance', 10, 2);
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
        Schema::dropIfExists('i_n_pandit_wallets');
    }
};
