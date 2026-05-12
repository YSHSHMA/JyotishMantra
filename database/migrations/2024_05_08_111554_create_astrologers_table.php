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
        Schema::create('astrologers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->integer('mobile_no');
            $table->string('image');
            $table->string('gender',10);
            $table->date('dob');
            $table->string('category');
            $table->string('primary_skills');
            $table->string('all_skills');
            $table->string('language',15);
            $table->double('charge');
            $table->double('video_charge');
            $table->double('report_charge');
            $table->double('experience');
            $table->double('daily_hours_contribution');
            $table->string('hear_about');
            $table->string('other_platform',10);
            $table->string('onboard_you');
            $table->string('interview_time');
            $table->string('city');
            $table->string('business_source',100);
            $table->string('qualification',100);
            $table->string('degree',100);
            $table->string('college');
            $table->string('learn_astrology');
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('website')->nullable();
            $table->string('is_refered',10);
            $table->string('referer_name')->nullable();
            $table->string('min_earning');
            $table->string('max_earning');
            $table->text('bio');
            $table->string('foreign_country',10);
            $table->string('working');
            $table->text('qualities');
            $table->text('challange');
            $table->text('repeat_question');
            $table->boolean('verified');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologers');
    }
};
