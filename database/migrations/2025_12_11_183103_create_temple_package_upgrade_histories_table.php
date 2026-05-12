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
        Schema::create('temple_package_upgrade_histories', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->index();
            $table->unsignedBigInteger('temple_id')->nullable();
            $table->unsignedBigInteger('trust_id')->nullable();
            $table->unsignedBigInteger('purohit_id')->nullable();
            $table->unsignedBigInteger('old_package_id')->nullable();
            $table->unsignedBigInteger('new_package_id')->nullable();
            $table->decimal('old_amount', 10, 2)->default(0);
            $table->decimal('new_amount', 10, 2)->default(0);
            $table->timestamp('upgraded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temple_package_upgrade_histories');
    }
};
