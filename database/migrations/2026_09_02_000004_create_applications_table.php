<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coach_profile_id')->constrained()->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->text('available_schedule')->nullable();
            $table->text('condition_note')->nullable();
            $table->string('attachment_url')->nullable();
            $table->string('status')->default('applied');
            $table->timestamps();
            $table->unique(['job_id', 'coach_profile_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
