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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained('group_schedules')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();

            // Owner account (for scoping and reporting)
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();

            // Who actually scanned/registered this row
            $table->foreignId('created_by')->nullable()->constrained('teachers')->nullOnDelete();

            $table->date('attendance_date');
            $table->time('check_in_time')->nullable();
            $table->string('status', 20)->default('present'); // present, late, absent, excused
            $table->boolean('is_auto_closed')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Prevent duplicate attendance per student/schedule/day
            $table->unique(['student_id', 'schedule_id', 'attendance_date'], 'unique_attendance');

            $table->index(['teacher_id', 'attendance_date']);
            $table->index(['group_id', 'attendance_date']);
            $table->index(['branch_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
