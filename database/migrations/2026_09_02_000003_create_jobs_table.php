<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('job_type');
            $table->string('prefecture');
            $table->string('area')->nullable();
            $table->text('required_conditions')->nullable();
            $table->string('target_age')->nullable();
            $table->string('gender')->nullable();
            $table->string('sport')->nullable();
            $table->string('request_frequency')->nullable();
            $table->string('request_style')->nullable();
            $table->text('role_description')->nullable();
            $table->text('detail')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('published');
            $table->date('publish_start_at')->nullable();
            $table->date('publish_end_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
};
