<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('grade');
            $table->foreignId('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('teachers')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['exam_id', 'group_id']);
            $table->index(['branch_id', 'grade', 'teacher_subject_id']);
            $table->index(['group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_targets');
    }
};
