<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('role')->default('student')->after('password');
            $table->string('avatar_path')->nullable()->after('role');
            $table->date('birth_date')->nullable()->after('avatar_path');
            $table->integer('points_total')->default(0)->after('birth_date');
            $table->boolean('is_active')->default(true)->after('points_total');
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'avatar_path', 'birth_date', 'points_total', 'is_active']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
