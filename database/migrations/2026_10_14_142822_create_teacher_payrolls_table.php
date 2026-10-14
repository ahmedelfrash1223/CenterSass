<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_payroll_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('teacher_id')
                ->unique()
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->string('payment_type')->default('monthly');

            $table->decimal('monthly_salary', 10, 2)->default(0);
            $table->decimal('per_session_rate', 10, 2)->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->decimal('session_deduction_rate', 10, 2)->default(0);
            $table->decimal('minimum_payout', 10, 2)->nullable();

            $table->unsignedTinyInteger('pay_day')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('payment_type');
        });

        Schema::create('teacher_branch_payroll_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            $table->string('payment_type');
            $table->decimal('monthly_salary', 10, 2)->default(0);
            $table->decimal('per_session_rate', 10, 2)->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->decimal('session_deduction_rate', 10, 2)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['teacher_id', 'branch_id']);
        });

        Schema::create('teacher_payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->date('period_start');
            $table->date('period_end');
            $table->string('period_label');

            $table->string('payment_type');

            $table->unsignedInteger('total_sessions')->default(0);
            $table->unsignedInteger('cancelled_sessions')->default(0);
            $table->unsignedInteger('payable_sessions')->default(0);
            $table->decimal('total_hours', 10, 2)->default(0);

            $table->decimal('base_salary', 10, 2)->default(0);
            $table->decimal('sessions_amount', 10, 2)->default(0);
            $table->decimal('hours_amount', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);

            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('status')->default('pending');

            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['teacher_id', 'period_start', 'period_end', 'branch_id'],
                'payroll_period_unique'
            );

            $table->index(['teacher_id', 'period_start']);
            $table->index('status');
        });

        Schema::create('teacher_payroll_bonuses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_id')
                ->constrained('teacher_payrolls')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('reason');

            $table->unsignedBigInteger('expense_id')->nullable();

            $table->foreignId('added_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('teacher_payroll_deductions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_id')
                ->constrained('teacher_payrolls')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('reason');

            $table->foreignId('added_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('teacher_payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            $table->foreignId('payroll_id')
                ->constrained('teacher_payrolls')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('cash');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('paid_at');
            $table->foreignId('paid_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->unsignedBigInteger('expense_id')->nullable();

            $table->timestamps();

            $table->index(['teacher_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_payroll_payments');
        Schema::dropIfExists('teacher_payroll_deductions');
        Schema::dropIfExists('teacher_payroll_bonuses');
        Schema::dropIfExists('teacher_payrolls');
        Schema::dropIfExists('teacher_branch_payroll_settings');
        Schema::dropIfExists('teacher_payroll_settings');
    }
};
