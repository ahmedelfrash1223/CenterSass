<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->string('day', 20); // Saturday, Sunday, Monday, etc.
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['group_id', 'day']);
            $table->unique(['group_id', 'day', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_schedules');
    }
};
