<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->boolean('is_student')->default(false)->after('birth_year');
        });
    }

    public function down()
    {
        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->dropColumn('is_student');
        });
    }
};
