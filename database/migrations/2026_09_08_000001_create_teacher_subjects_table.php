<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('grade');
            $table->string('subject');
            $table->unsignedTinyInteger('is_active')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('teachers')->nullOnDelete();

            $table->timestamps();
            $table->index(['teacher_id', 'is_active']);
            $table->unique(['teacher_id', 'grade', 'subject']);
            $table->index(['teacher_id', 'grade']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_subjects');
    }
};
