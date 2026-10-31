<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The teacher sidebar now has a single fixed design, so the per-teacher sidebar theme is no longer stored.
     */
    public function up(): void
    {
        if (Schema::hasColumn('teacher_theme_settings', 'sidebar_theme')) {
            Schema::table('teacher_theme_settings', function (Blueprint $table) {
                $table->dropColumn('sidebar_theme');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('teacher_theme_settings', 'sidebar_theme')) {
            Schema::table('teacher_theme_settings', function (Blueprint $table) {
                $table->string('sidebar_theme')->default('light')->after('theme_mode');
            });
        }
    }
};
