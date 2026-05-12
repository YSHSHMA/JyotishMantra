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
        Schema::create('pandits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->integer('mobile_no');
            $table->string('image');
            $table->string('gender',10);
            $table->date('dob');
            $table->string('maritial',20);
            $table->string('city');
            $table->text('address');
            $table->text('bio');
            $table->string('qualification');
            $table->string('college');
            $table->string('qualification_image');
            $table->string('language_known');
            $table->string('category');
            $table->text('pooja');
            $table->string('experties');
            $table->string('experience');
            $table->string('business_source',100);
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('website')->nullable();
            $table->string('panda');
            $table->string('gotra');
            $table->string('primary_mandir');
            $table->string('primary_mandir_location');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pandits');
    }
};
