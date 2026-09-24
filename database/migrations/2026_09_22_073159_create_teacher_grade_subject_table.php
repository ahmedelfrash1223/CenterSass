<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('grade');
            $table->timestamps();

            $table->unique(['teacher_id', 'grade']);
        });

        Schema::create('teacher_grade_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['teacher_id', 'teacher_subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_grade_subjects');
        Schema::dropIfExists('teacher_grades');
    }
};
