<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->string('status')->default('pending');
            $table->unsignedInteger('cards_count')->default(0);
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();

            $table->index(['teacher_id', 'status']);
        });

        Schema::create('card_request_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('card_request_id')
                ->constrained('card_requests')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->string('student_name');
            $table->string('student_code')->nullable();
            $table->string('grade')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();

            $table->timestamps();

            $table->unique(['card_request_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_request_items');
        Schema::dropIfExists('card_requests');
    }
};
