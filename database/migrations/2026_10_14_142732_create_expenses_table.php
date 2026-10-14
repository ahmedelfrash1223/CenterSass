<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            $table->foreignId('teacher_id')
                ->comment('الأونر صاحب الحساب')
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

            $table->foreignId('teacher_payee_id')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->unsignedBigInteger('teacher_payroll_id')
                ->nullable()
                ->comment('كشف الحساب المرتبط (FK هيضاف لاحقًا)');

            $table->string('description')->nullable();
            $table->json('meta')->nullable();

            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['teacher_id', 'source']);
            $table->index(['teacher_id', 'occurred_at']);
            $table->index(['teacher_payee_id', 'source']);
            $table->index('teacher_payroll_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
