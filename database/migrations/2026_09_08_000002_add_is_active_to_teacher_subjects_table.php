<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_active')->default(1)->after('subject');
            $table->index(['teacher_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->dropIndex(['teacher_id', 'is_active']);
            $table->dropColumn('is_active');
        });
    }
};