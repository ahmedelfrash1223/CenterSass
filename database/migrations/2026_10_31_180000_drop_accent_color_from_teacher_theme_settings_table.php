<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The teacher dashboard now has one fixed Nabza color identity, so the
     * per-teacher accent color (template "preset-N") is no longer stored.
     * theme_mode (light / dark / system) and layout_width are kept.
     */
    public function up(): void
    {
        if (Schema::hasColumn('teacher_theme_settings', 'accent_color')) {
            Schema::table('teacher_theme_settings', function (Blueprint $table) {
                $table->dropColumn('accent_color');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('teacher_theme_settings', 'accent_color')) {
            Schema::table('teacher_theme_settings', function (Blueprint $table) {
                $table->string('accent_color')->default('preset-1')->after('theme_mode');
            });
        }
    }
};
