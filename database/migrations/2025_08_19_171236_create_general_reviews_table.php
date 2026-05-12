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
        Schema::create('general_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('review_type',100);
            $table->bigInteger('review_ref_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('user_name',150)->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('is_anonymous')->default(0)->comment('0=show user, 1=anonymous');
            $table->text('review_text');
            $table->tinyInteger('star_rating')->nullable();
            $table->string('video_url')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=approved, 2=blocked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_reviews');
    }
};
