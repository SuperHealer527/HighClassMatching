<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->boolean('show_birth_year')->default(false);
            $table->boolean('show_available_prefectures')->default(true);
            $table->boolean('show_request_history')->default(false);
            $table->boolean('show_recommended_athlete')->default(false);
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->foreignId('coach_profile_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->text('admin_reply')->nullable()->after('admin_note');
            $table->foreignId('replied_by')->nullable()->after('admin_reply')->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable()->after('replied_by');
        });
    }

    public function down()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coach_profile_id');
            $table->dropConstrainedForeignId('replied_by');
            $table->dropColumn(['admin_reply', 'replied_at']);
        });

        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'show_birth_year', 'show_available_prefectures',
                'show_request_history', 'show_recommended_athlete',
            ]);
        });
    }
};
