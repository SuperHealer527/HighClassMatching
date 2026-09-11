<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('application_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        DB::table('applications')->orderBy('id')->get()->each(function ($application) {
            DB::table('application_status_histories')->insert([
                'application_id' => $application->id,
                'changed_by' => null,
                'from_status' => null,
                'to_status' => $application->status,
                'note' => '既存の応募データから移行しました。',
                'created_at' => $application->created_at,
                'updated_at' => $application->updated_at,
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('application_status_histories');
    }
};
