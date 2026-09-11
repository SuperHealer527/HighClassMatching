<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('status');
        });

        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->string('identity_document_path')->nullable();
            $table->string('qualification_document_path')->nullable();
            $table->string('verification_status')->default('pending');
            $table->unsignedTinyInteger('completeness_score')->default(0);
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->string('image_path')->nullable();
            $table->text('introduction')->nullable();
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->string('image_path')->nullable();
            $table->string('budget_note')->nullable();
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coach_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->text('message');
            $table->string('proposed_schedule')->nullable();
            $table->string('status')->default('sent');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('coach_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coach_profile_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['organization_id', 'coach_profile_id']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coach_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('title');
            $table->text('body');
            $table->string('status')->default('published');
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('interview');
            $table->text('excerpt');
            $table->longText('body');
            $table->string('cover_image_path')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('matching_masters', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('value');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['type', 'value']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('matching_masters');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('coach_favorites');
        Schema::dropIfExists('offers');
        Schema::table('jobs', fn (Blueprint $table) => $table->dropColumn(['image_path', 'budget_note']));
        Schema::table('organizations', fn (Blueprint $table) => $table->dropColumn(['image_path', 'introduction']));
        Schema::table('coach_profiles', fn (Blueprint $table) => $table->dropColumn(['identity_document_path', 'qualification_document_path', 'verification_status', 'completeness_score']));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('last_login_at'));
    }
};
