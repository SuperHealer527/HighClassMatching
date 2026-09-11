<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('coach_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('kana')->nullable();
            $table->string('roman_name')->nullable();
            $table->year('birth_year')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('main_prefecture');
            $table->json('available_prefectures')->nullable();
            $table->string('area')->nullable();
            $table->json('sports')->nullable();
            $table->json('fields')->nullable();
            $table->string('degree')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('other_qualifications')->nullable();
            $table->text('keywords')->nullable();
            $table->text('target_ages')->nullable();
            $table->text('target_levels')->nullable();
            $table->text('teaching_styles')->nullable();
            $table->text('achievements')->nullable();
            $table->text('request_history')->nullable();
            $table->boolean('is_former_athlete')->default(false);
            $table->string('recommended_athlete')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('desired_fee_range')->nullable();
            $table->text('message')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('profile_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('coach_profiles');
    }
};
