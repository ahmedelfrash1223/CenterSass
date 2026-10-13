<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->string('source');

            $table->unsignedBigInteger('source_id')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EGP');

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->foreignId('student_id')
                ->nullable()
                ->constrained('students')
                ->nullOnDelete();

            $table->string('description')->nullable();
            $table->json('meta')->nullable();

            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['teacher_id', 'source']);
            $table->index(['teacher_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
