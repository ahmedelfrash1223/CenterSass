<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Unique receipt number: REC-2026-00001
            $table->string('receipt_number', 30)->unique();

            // Relations
            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            // Amount paid
            $table->decimal('amount', 12, 2);

            // Payment method
            $table->string('payment_method', 30);
            $table->string('reference')->nullable();

            // When paid
            $table->date('paid_at');
            $table->time('paid_time')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index(['teacher_id', 'paid_at']);
            $table->index('invoice_id');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
