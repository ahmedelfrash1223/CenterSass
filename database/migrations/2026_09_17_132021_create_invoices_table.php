<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Unique invoice number: INV-2026-00001
            $table->string('invoice_number', 30)->unique();

            // Relations
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Billing period
            $table->date('period_start');
            $table->date('period_end');
            $table->string('period_label', 50);

            // Amounts
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('extra', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);

            // Status
            $table->string('status', 20)->default('unpaid');

            // Breakdown snapshot (JSON)
            $table->json('breakdown')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['teacher_id', 'status']);
            $table->index(['student_id', 'period_start']);
            $table->index(['teacher_id', 'period_start', 'period_end'], 'inv_teacher_period_idx');
            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
