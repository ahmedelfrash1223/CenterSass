<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_theme_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')
                ->unique()
                ->constrained('admins')
                ->cascadeOnDelete();

            $table->string('theme_mode')->default('light');
            $table->string('sidebar_theme')->default('light');
            $table->string('accent_color')->default('preset-1');
            $table->string('layout_width')->default('full');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_theme_settings');
    }
};
