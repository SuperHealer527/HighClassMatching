<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProjectMembersTable extends Migration
{
    public function up()
    {
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('role', 40);
            $table->string('affiliation', 20);
            $table->string('image_path');
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        $now = now();
        DB::table('project_members')->insert([
            ['name' => '上水 研一朗', 'role' => 'PROJECT LEAD', 'affiliation' => '東海大学 上水研究室', 'image_path' => 'images/team-member-01.png', 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '高橋 美咲', 'role' => 'OPERATIONS', 'affiliation' => 'Back Athlete機構', 'image_path' => 'images/team-member-02.png', 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '佐藤 健一', 'role' => 'SUPERVISOR', 'affiliation' => 'スポーツ科学研究センター', 'image_path' => 'images/team-member-03.png', 'sort_order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '中村 由佳', 'role' => 'COORDINATOR', 'affiliation' => '地域スポーツ推進室', 'image_path' => 'images/team-member-04.png', 'sort_order' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '伊藤 真理', 'role' => 'ADVISOR', 'affiliation' => 'アスリート支援事業部', 'image_path' => 'images/team-member-05.png', 'sort_order' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '山本 大輔', 'role' => 'DEVELOPER', 'affiliation' => '株式会社ハイクラス', 'image_path' => 'images/team-member-06.png', 'sort_order' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '小林 彩香', 'role' => 'DESIGNER', 'affiliation' => '株式会社ハイクラス', 'image_path' => 'images/team-member-07.png', 'sort_order' => 7, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '松本 拓也', 'role' => 'ENGINEER', 'affiliation' => '株式会社ハイクラス', 'image_path' => 'images/team-member-08.png', 'sort_order' => 8, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('project_members');
    }
}
