<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_account_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_account_id')->constrained('student_accounts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamp('linked_at')->useCurrent();
            $table->timestamps();
            $table->unique(['student_account_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_account_student');
    }
};
