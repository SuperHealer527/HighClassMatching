<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referrer')->nullable()->after('member_type');
        });

        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->json('other_affiliations')->nullable()->after('affiliation');
        });
    }

    public function down()
    {
        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->dropColumn('other_affiliations');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referrer');
        });
    }
};
