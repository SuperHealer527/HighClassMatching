<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->json('education_history')->nullable();
            $table->json('qualification_items')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('teaching_achievements')->nullable();
            $table->json('request_achievements')->nullable();
            $table->boolean('direct_offer_enabled')->default(true);
        });

        DB::table('coach_profiles')->orderBy('id')->chunkById(100, function ($coaches) {
            foreach ($coaches as $coach) {
                $qualifications = preg_split('/[、,\r\n]+/u', (string) $coach->qualifications, -1, PREG_SPLIT_NO_EMPTY);
                DB::table('coach_profiles')->where('id', $coach->id)->update([
                    'education_history' => json_encode(array_values(array_filter([(string) $coach->degree])), JSON_UNESCAPED_UNICODE),
                    'qualification_items' => json_encode(array_slice(array_values($qualifications ?: []), 0, 2), JSON_UNESCAPED_UNICODE),
                    'recommendations' => json_encode($coach->recommended_athlete ? [['name' => $coach->recommended_athlete, 'introduction' => '']] : [], JSON_UNESCAPED_UNICODE),
                    'teaching_achievements' => json_encode(array_values(array_filter([(string) $coach->achievements])), JSON_UNESCAPED_UNICODE),
                    'request_achievements' => json_encode(array_values(array_filter([(string) $coach->request_history])), JSON_UNESCAPED_UNICODE),
                ]);
            }
        });
    }

    public function down()
    {
        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'education_history', 'qualification_items', 'recommendations',
                'teaching_achievements', 'request_achievements', 'direct_offer_enabled',
            ]);
        });
    }
};
