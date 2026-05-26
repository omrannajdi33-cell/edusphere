<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_submissions', function (Blueprint $table) {
            $table->boolean('counts_for_bulletin')->default(false)->after('max_score');
            $table->unsignedTinyInteger('percentage')->nullable()->after('counts_for_bulletin');
            $table->boolean('needs_manual_review')->default(false)->after('percentage');
            $table->foreignId('graded_by_id')->nullable()->after('needs_manual_review')->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable()->after('graded_by_id');
        });
    }

    public function down(): void
    {
        Schema::table('activity_submissions', function (Blueprint $table) {
            $table->dropForeign(['graded_by_id']);
            $table->dropColumn(['counts_for_bulletin', 'percentage', 'needs_manual_review', 'graded_by_id', 'graded_at']);
        });
    }
};
