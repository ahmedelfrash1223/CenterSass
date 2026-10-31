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
        Schema::create('teacher_theme_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')
                ->unique()
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->string('theme_mode')->default('light');
            $table->string('layout_width')->default('full');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_theme_settings');
    }
};
