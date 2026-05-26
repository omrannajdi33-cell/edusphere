<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedSmallInteger('exam_duration_minutes')->nullable()->after('grading_mode');
        });

        Schema::table('activity_submissions', function (Blueprint $table) {
            $table->json('grading_details')->nullable()->after('teacher_comment');
            $table->timestamp('exam_started_at')->nullable()->after('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('exam_duration_minutes');
        });

        Schema::table('activity_submissions', function (Blueprint $table) {
            $table->dropColumn(['grading_details', 'exam_started_at']);
        });
    }
};
